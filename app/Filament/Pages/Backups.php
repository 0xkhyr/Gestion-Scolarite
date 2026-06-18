<?php

namespace App\Filament\Pages;

use Throwable;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

/**
 * In-app backup management for the platform owner (super_admin only).
 * Lists archives on the configured backup disks, triggers a backup, and
 * downloads/deletes them — so backups are reachable without server/SSH access.
 *
 * Intentionally NOT exposed to school admins: in a multi-tenant setup raw DB
 * backups belong to the platform owner (a tenant "export my data" would be a
 * separate, scoped feature).
 */
class Backups extends Page
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-circle-stack';

    protected string $view = 'filament.pages.backups';

    protected static ?string $slug = 'backups';

    protected static ?int $navigationSort = 99;

    public static function getNavigationGroup(): ?string
    {
        return __('app.system');
    }

    public function getTitle(): string
    {
        return __('app.backups');
    }

    public static function getNavigationLabel(): string
    {
        return __('app.backups');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canAccess();
    }

    public static function canAccess(): bool
    {
        return (bool) auth()->user()?->hasRole('super_admin');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('backupNow')
                ->label(__('app.backup_now'))
                ->icon('heroicon-o-plus-circle')
                ->requiresConfirmation()
                ->modalDescription(__('app.backup_now_confirm'))
                ->action(fn () => $this->backupNow()),
        ];
    }

    /** Native Filament confirmation modal for per-row deletes (same as elsewhere). */
    public function deleteBackupAction(): Action
    {
        return Action::make('deleteBackup')
            ->requiresConfirmation()
            ->icon('heroicon-o-trash')
            ->color('danger')
            ->modalHeading(__('app.delete'))
            ->modalDescription(__('app.backup_delete_confirm'))
            ->modalSubmitActionLabel(__('app.delete'))
            ->action(function (array $arguments): void {
                $this->deleteBackup($arguments['disk'] ?? '', $arguments['file'] ?? '');
            });
    }

    public function backupNow(): void
    {
        try {
            Artisan::call('backup:run');

            Notification::make()
                ->title(__('app.backup_completed'))
                ->success()
                ->send();
        } catch (Throwable $e) {
            Notification::make()
                ->title(__('app.backup_failed'))
                ->body($e->getMessage())
                ->danger()
                ->persistent()
                ->send();
        }
    }

    /** Backups across all configured disks, newest first. */
    public function getBackups(): array
    {
        $name = config('backup.backup.name');
        $disks = config('backup.backup.destination.disks', ['local']);
        $rows = [];

        foreach ($disks as $diskName) {
            try {
                $storage = Storage::disk($diskName);

                foreach ($storage->files($name) as $path) {
                    if (! str_ends_with(strtolower($path), '.zip')) {
                        continue;
                    }

                    $rows[] = [
                        'disk' => $diskName,
                        'name' => basename($path),
                        'size' => $storage->size($path),
                        'timestamp' => $storage->lastModified($path),
                        'download_url' => URL::temporarySignedRoute(
                            'admin.backups.download',
                            now()->addMinutes(10),
                            ['disk' => $diskName, 'file' => basename($path)],
                        ),
                    ];
                }
            } catch (Throwable $e) {
                // Disk unreachable (e.g. S3 not configured yet) — skip it.
            }
        }

        usort($rows, fn ($a, $b) => $b['timestamp'] <=> $a['timestamp']);

        return $rows;
    }

    public function deleteBackup(string $disk, string $file): void
    {
        abort_unless(auth()->user()?->hasRole('super_admin'), 403);

        // Same hardening as the download endpoint: whitelist disk, basename only,
        // must match the backup prefix + .zip — no arbitrary path/disk deletes.
        $allowedDisks = config('backup.backup.destination.disks', ['local']);
        abort_unless(in_array($disk, $allowedDisks, true), 404);

        $file = basename($file);
        abort_unless((bool) preg_match('/^[A-Za-z0-9._-]+\.zip$/', $file), 404);

        $path = config('backup.backup.name') . '/' . $file;
        Storage::disk($disk)->delete($path);

        Notification::make()
            ->title(__('app.backup_deleted'))
            ->success()
            ->send();
    }

    /**
     * Whether archives are encrypted. The password is intentionally NOT exposed
     * here — it lives only in the server secret (BACKUP_ARCHIVE_PASSWORD), never
     * in the DB or the UI.
     */
    public function isEncrypted(): bool
    {
        return ! empty(config('backup.backup.password'));
    }

    public function formatSize(int $bytes): string
    {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        }
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        }
        if ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }

        return $bytes . ' B';
    }
}
