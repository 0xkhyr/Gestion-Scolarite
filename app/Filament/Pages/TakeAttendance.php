<?php

namespace App\Filament\Pages;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\CheckboxList;
use Filament\Schemas\Components\Actions;
use Filament\Actions\Action;
use App\Support\Academic;
use App\Models\Attendance;
use App\Models\Classe;
use App\Models\Cours;
use App\Models\Etudiant;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

/**
 * Bulk per-session attendance marking. Pick a class, a timetabled cours and a
 * date, then tick who is present (binary present/absent). Part of the optional
 * attendance module — gated behind feature('attendance').
 */
class TakeAttendance extends Page
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected string $view = 'filament.pages.take-attendance';

    protected static ?string $slug = 'attendance/take';

    protected static ?int $navigationSort = 3;

    public ?array $data = [];

    public static function getNavigationGroup(): ?string
    {
        return __('app.gestion_academique');
    }

    public function getTitle(): string
    {
        return __('app.take_attendance');
    }

    public static function getNavigationLabel(): string
    {
        return __('app.take_attendance');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return feature('attendance') && static::userCanMark();
    }

    public static function canAccess(): bool
    {
        return feature('attendance') && static::userCanMark();
    }

    /** Who may mark: admins/secretary (any) or teachers (own classes). */
    protected static function userCanMark(): bool
    {
        return (bool) auth()->user()?->hasAnyRole(['super_admin', 'admin', 'secretary', 'teacher', 'enseignant']);
    }

    /** Teacher = a teacher who is not also an admin/secretary (those see everything). */
    protected function isScopedTeacher(): bool
    {
        $user = auth()->user();

        return $user->hasAnyRole(['teacher', 'enseignant'])
            && ! $user->hasAnyRole(['super_admin', 'admin', 'secretary']);
    }

    public function mount(): void
    {
        $this->form->fill(['date' => now()->toDateString()]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('app.attendance_session'))
                    ->description(__('app.attendance_session_desc'))
                    ->schema([
                        Select::make('id_classe')
                            ->label(__('app.classe'))
                            ->options(fn () => $this->classeOptions())
                            ->required()
                            ->searchable()
                            ->live()
                            ->afterStateUpdated(function (Set $set) {
                                $set('id_cours', null);
                                $set('present_students', []);
                            }),

                        Select::make('id_cours')
                            ->label(__('app.cours'))
                            ->options(fn (Get $get) => $this->coursOptions($get('id_classe')))
                            ->required()
                            ->live()
                            ->afterStateUpdated(fn (Get $get, Set $set) => $set(
                                'present_students',
                                $this->resolvePresent($get('id_classe'), $get('id_cours'), $get('date'))
                            )),

                        DatePicker::make('date')
                            ->label(__('app.date'))
                            ->required()
                            ->default(now())
                            ->maxDate(now())
                            ->live()
                            ->afterStateUpdated(fn (Get $get, Set $set) => $set(
                                'present_students',
                                $this->resolvePresent($get('id_classe'), $get('id_cours'), $get('date'))
                            )),
                    ])->columns(3),

                Section::make(__('app.attendance_roster'))
                    ->description(__('app.attendance_roster_desc'))
                    ->visible(fn (Get $get) => filled($get('id_classe')) && filled($get('id_cours')) && filled($get('date')))
                    ->schema([
                        CheckboxList::make('present_students')
                            ->label(__('app.present_students'))
                            ->helperText(__('app.present_students_help'))
                            ->options(fn (Get $get) => $this->rosterOptions($get('id_classe')))
                            ->bulkToggleable()
                            ->columns(2),
                    ]),

                Actions::make([
                    Action::make('save')
                        ->label(__('app.save_attendance'))
                        ->icon('heroicon-m-check-circle')
                        ->color('primary')
                        ->action(fn () => $this->save()),
                ]),
            ])
            ->statePath('data');
    }

    protected function classeOptions(): array
    {
        $query = Classe::query()->orderBy('niveau')->orderBy('nom_classe');

        if ($this->isScopedTeacher()) {
            $teacherClasses = auth()->user()->profile?->classes()->pluck('classes.id_classe') ?? collect();
            $query->whereIn('id_classe', $teacherClasses);
        }

        return $query->get()->mapWithKeys(fn ($c) => [
            $c->id_classe => $c->nom_classe . ' (' . Academic::levelLabel($c->niveau)
                . ($c->serie ? ' ' . $c->serie : '') . ')',
        ])->all();
    }

    protected function coursOptions(?int $classeId): array
    {
        if (! $classeId) {
            return [];
        }

        $query = Cours::with('matiere')->where('id_classe', $classeId);

        if ($this->isScopedTeacher()) {
            $query->where('id_enseignant', auth()->user()->profile?->id_enseignant);
        }

        return $query->get()->mapWithKeys(function ($cours) {
            $subject = $cours->matiere?->nom_matiere ?? $cours->matiere ?? '—';
            $label = ucfirst($cours->jour) . ' ' . substr((string) $cours->date_debut, 0, 5)
                . '–' . substr((string) $cours->date_fin, 0, 5) . ' · ' . $subject;

            return [$cours->id_cours => $label];
        })->all();
    }

    protected function rosterOptions(?int $classeId): array
    {
        if (! $classeId) {
            return [];
        }

        return Etudiant::where('id_classe', $classeId)
            ->orderBy('nom')->orderBy('prenom')
            ->get()
            ->mapWithKeys(fn ($e) => [
                $e->id_etudiant => trim($e->prenom . ' ' . $e->nom) . ' (' . $e->matricule . ')',
            ])->all();
    }

    /**
     * Existing record? return the present students for that session. Otherwise
     * default everyone to present (the common case — only mark absentees).
     */
    protected function resolvePresent(?int $classeId, ?int $coursId, ?string $date): array
    {
        if (! $classeId || ! $coursId || ! $date) {
            return [];
        }

        $existing = Attendance::where('id_cours', $coursId)->whereDate('date', $date)->get();

        if ($existing->isNotEmpty()) {
            return $existing->where('status', 'present')->pluck('id_etudiant')->all();
        }

        return Etudiant::where('id_classe', $classeId)->pluck('id_etudiant')->all();
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $classeId = (int) $data['id_classe'];
        $coursId = (int) $data['id_cours'];
        $date = $data['date'];
        $present = $data['present_students'] ?? [];

        // Defence in depth: a scoped teacher may only mark their own cours.
        if ($this->isScopedTeacher()) {
            $owns = Cours::where('id_cours', $coursId)
                ->where('id_enseignant', auth()->user()->profile?->id_enseignant)
                ->exists();

            if (! $owns) {
                Notification::make()->title(__('app.attendance_not_allowed'))->danger()->send();

                return;
            }
        }

        $students = Etudiant::where('id_classe', $classeId)->pluck('id_etudiant');

        foreach ($students as $studentId) {
            Attendance::updateOrCreate(
                ['id_etudiant' => $studentId, 'id_cours' => $coursId, 'date' => $date],
                [
                    'id_classe' => $classeId,
                    'status' => in_array($studentId, $present) ? 'present' : 'absent',
                    'marked_by' => auth()->id(),
                ],
            );
        }

        Notification::make()
            ->title(__('app.attendance_saved'))
            ->body(__('app.attendance_saved_body', [
                'present' => count($present),
                'total' => $students->count(),
            ]))
            ->success()
            ->send();
    }
}
