<?php

namespace App\Filament\Resources;

use App\Models\User;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Placeholder;
use Illuminate\Support\Facades\Blade;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Filament\Actions\ViewAction;
use App\Filament\Resources\ActivityLogResource\Pages\ListActivityLogs;
use App\Filament\Resources\ActivityLogResource\Pages\ViewActivityLog;
use App\Filament\Concerns\HasRoleBasedAccess;
use App\Filament\Resources\ActivityLogResource\Pages;
use Spatie\Activitylog\Models\Activity as ActivityModel;

use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;

class ActivityLogResource extends Resource
{
    use HasRoleBasedAccess;
    
    protected static ?string $model = ActivityModel::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-list-bullet';
    protected static ?int $navigationSort = 4;

    public static function getNavigationGroup(): ?string
    {
        return __('app.systeme');
    }

    public static function getNavigationLabel(): string
    {
        return __('app.activity_logs');
    }

    public static function getPluralLabel(): string
    {
        return __('app.activity_logs');
    }

    public static function getModelLabel(): string
    {
        return __('app.activity_log');
    }

    public static function canViewAny(): bool
    {
        $user = auth()->user();

        if (! $user) {
            return false;
        }

        return $user->hasPermissionTo('activity_log.view');
    }

    public static function canCreate(): bool
    {
        return false; // Activity logs should not be manually created
    }

    public static function canEdit($record): bool
    {
        return false; // Activity logs should not be edited
    }

    public static function canDelete($record): bool
    {
        return false; // Activity logs should not be deleted for audit integrity
    }

    /**
     * Build a link to the causer's user record, addressed directly by user id
     * (causer_id). Returns null — so the name falls back to plain text — when the
     * causer is missing/deleted, isn't a User, or the viewer can't open it.
     */
    protected static function causerUrl($record): ?string
    {
        if (! $record?->causer_id || ! $record->causer instanceof User) {
            return null;
        }

        if (! UserResource::canViewAny()) {
            return null;
        }

        return UserResource::getUrl('view', ['record' => $record->causer_id]);
    }

    /** Map an event (or fall back to description keywords) to a Filament badge color. */
    protected static function eventColor(?string $event, ?string $description = null): string
    {
        $haystack = strtolower(($event ?? '') . ' ' . ($description ?? ''));

        return match (true) {
            $event === 'created' => 'success',
            $event === 'updated' => 'warning',
            $event === 'deleted' => 'danger',
            str_contains($haystack, 'brute force')
                || str_contains($haystack, 'blocked')
                || str_contains($haystack, 'failed')
                || str_contains($haystack, 'unauthorized')
                || str_contains($haystack, 'denied') => 'danger',
            str_contains($haystack, 'login') || str_contains($haystack, 'enabled') => 'success',
            str_contains($haystack, 'logout') || str_contains($haystack, 'disabled') => 'gray',
            default => 'info',
        };
    }

    /** Short human label for the event badge. */
    protected static function eventLabel(?string $event, ?string $description = null): string
    {
        if ($event) {
            return __('app.' . $event);
        }

        $haystack = strtolower($description ?? '');

        return match (true) {
            str_contains($haystack, 'brute force') => __('app.security'),
            str_contains($haystack, 'unauthorized') || str_contains($haystack, 'denied') => __('app.access_denied'),
            str_contains($haystack, 'failed') => __('app.failed_login'),
            str_contains($haystack, 'login') => __('app.login'),
            str_contains($haystack, 'logout') => __('app.logout'),
            default => __('app.activity'),
        };
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('app.log_details') ?? 'Log Details')
                ->schema([
                    Placeholder::make('log_name')
                        ->label(__('app.log'))
                        ->content(fn ($record) => $record?->log_name
                            ? new HtmlString('<span class="text-sm text-gray-600">' . e($record->log_name) . '</span>')
                            : null
                        )
                        ->columnSpan(1),

                    Placeholder::make('event')
                        ->label(__('app.action'))
                        ->content(fn ($record) => new HtmlString(Blade::render(
                            '<div class="flex"><x-filament::badge :color="$color">{{ $label }}</x-filament::badge></div>',
                            [
                                'color' => self::eventColor($record?->event, $record?->description),
                                'label' => self::eventLabel($record?->event, $record?->description),
                            ],
                        )))
                        ->columnSpan(1),

                    Placeholder::make('description')
                        ->label(__('app.description'))
                        ->content(fn ($record) => $record?->description
                            ? new HtmlString('<div class="text-sm text-gray-700">' . e($record->description) . '</div>')
                            : null
                        )
                        ->columnSpanFull(),

                    Placeholder::make('causer')
                        ->label(__('app.causer'))
                        ->content(function ($record) {
                            if (! $record?->causer_id) {
                                return null;
                            }

                            $name = $record->causer?->name
                                ?? (class_basename($record->causer_type) . " #{$record->causer_id}");

                            $url = self::causerUrl($record);

                            if ($url) {
                                return new HtmlString(
                                    '<a href="' . e($url) . '" class="text-sm font-medium text-primary-600 hover:underline">'
                                    . e($name) . '</a>'
                                );
                            }

                            return new HtmlString('<div class="text-sm font-medium">' . e($name) . '</div>');
                        })
                        ->columnSpan(1),

                    Placeholder::make('subject')
                        ->label(__('app.subject'))
                        ->content(fn ($record) => $record?->subject_id
                            ? new HtmlString('<div class="text-sm">' . e(class_basename($record->subject_type) . " #{$record->subject_id}") . '</div>')
                            : null
                        )
                        ->columnSpan(1),

                    Placeholder::make('properties')
                        ->label(__('app.changes'))
                        ->content(fn ($record) => new HtmlString(
                            '<pre class="rounded bg-gray-50 p-3 text-xs font-mono text-gray-700" style="white-space:pre-wrap;word-break:break-word;">' .
                                e(json_encode($record->properties ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) .
                            '</pre>'
                        ))
                        ->columnSpanFull(),

                    Placeholder::make('ip')
                        ->label(__('app.ip_address'))
                        ->content(fn ($record) => $record->properties['ip_address'] ?? null)
                        ->columnSpan(1),

                    Placeholder::make('user_agent')
                        ->label(__('app.user_agent'))
                        ->content(fn ($record) => $record->properties['user_agent'] ?? null)
                        ->columnSpan(1),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label(__('app.time'))
                    ->dateTime('d M Y H:i')
                    ->description(fn ($record) => $record->created_at?->diffForHumans())
                    ->sortable(),

                TextColumn::make('event')
                    ->label(__('app.event'))
                    ->badge()
                    ->color(fn ($record) => self::eventColor($record->event, $record->description))
                    ->formatStateUsing(fn ($record) => self::eventLabel($record->event, $record->description))
                    ->sortable(),

                TextColumn::make('causer.name')
                    ->label(__('app.user'))
                    ->icon('heroicon-m-user')
                    ->default('—')
                    ->url(fn ($record) => self::causerUrl($record))
                    ->openUrlInNewTab()
                    ->color(fn ($record) => self::causerUrl($record) ? 'primary' : null)
                    ->searchable(),

                TextColumn::make('description')
                    ->label(__('app.actions'))
                    ->wrap()
                    ->searchable()
                    ->limit(80),

                TextColumn::make('subject_type')
                    ->label(__('app.resource'))
                    ->badge()
                    ->color('gray')
                    ->formatStateUsing(fn ($state, $record) => $state
                        ? class_basename($state) . ($record->subject_id ? " #{$record->subject_id}" : '')
                        : null)
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('properties->ip_address')
                    ->label(__('app.ip_address'))
                    ->icon('heroicon-m-globe-alt')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // Filter by causer (used by the "activity trail" link from a user).
                SelectFilter::make('causer_id')
                    ->label(__('app.user'))
                    ->searchable()
                    ->options(fn () => User::query()
                        ->whereIn('id', ActivityModel::query()
                            ->whereNotNull('causer_id')
                            ->distinct()
                            ->pluck('causer_id'))
                        ->pluck('name', 'id')
                        ->toArray()),

                SelectFilter::make('log_name')
                    ->label(__('app.log_name'))
                    ->options(fn () => ActivityModel::query()
                        ->distinct()
                        ->whereNotNull('log_name')
                        ->pluck('log_name', 'log_name')
                        ->filter()
                        ->toArray()
                    ),

                SelectFilter::make('event')
                    ->label(__('app.event'))
                    ->options(fn () => ActivityModel::query()
                        ->distinct()
                        ->whereNotNull('event')
                        ->pluck('event', 'event')
                        ->filter()
                        ->toArray()
                    ),

                Filter::make('date')
                    ->schema([
                        DatePicker::make('from')->label(__('app.from')),
                        DatePicker::make('to')->label(__('app.to')),
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (! empty($data['from'])) {
                            $query->whereDate('created_at', '>=', $data['from']);
                        }

                        if (! empty($data['to'])) {
                            $query->whereDate('created_at', '<=', $data['to']);
                        }
                    }),
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->toolbarActions([])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListActivityLogs::route('/'),
            'view' => ViewActivityLog::route('/{record}'),
        ];
    }
}
