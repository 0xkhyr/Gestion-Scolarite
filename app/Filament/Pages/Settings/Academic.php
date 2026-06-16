<?php

namespace App\Filament\Pages\Settings;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use App\Services\SettingsService;

class Academic extends Page
{
    protected static ?string $navigationIcon = null;
    
    protected static string $view = 'filament.pages.settings.academic';
    

    public function getTitle(): string
    {
        return __('app.academic_settings');
    }
    
    protected static ?string $slug = 'settings/academic';
    
    protected static bool $shouldRegisterNavigation = false;

    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole('super_admin') || auth()->user()?->hasPermissionTo('setting.manage');
    }

    public ?array $data = [];

    protected SettingsService $settingsService;

    public function boot(SettingsService $settingsService): void
    {
        $this->settingsService = $settingsService;
    }

    public function mount(): void
    {
        $academicSettings = $this->settingsService->getAcademicSettings();
        
        $this->form->fill([
            'grading_system' => $academicSettings['academic.grading_system'],
            'passing_grade' => $academicSettings['academic.passing_grade'],
            'max_grade' => $academicSettings['academic.max_grade'],
            'terms_per_year' => $academicSettings['academic.terms_per_year'],
            'attendance_required' => $academicSettings['academic.attendance_required'],
            'min_attendance_percentage' => $academicSettings['academic.min_attendance_percentage'],
            'late_submission_penalty' => $academicSettings['academic.late_submission_penalty'],
            'max_absences_per_term' => $academicSettings['academic.max_absences_per_term'],
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('app.grading_system'))
                    ->description(__('app.grading_system_desc'))
                    ->schema([
                        Forms\Components\Select::make('grading_system')
                            ->label(__('app.grading_system'))
                            ->options([
                                'sur_20' => __('app.grading_sur_20'),
                                'gpa' => __('app.grading_gpa'),
                                'letter' => __('app.grading_letter'),
                            ])
                            ->default('sur_20')
                            ->required(),
                        Forms\Components\TextInput::make('passing_grade')
                            ->label(__('app.passing_grade'))
                            ->helperText(__('app.passing_grade_help'))
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(20)
                            ->suffix('/20')
                            ->default(10)
                            ->required(),
                        Forms\Components\TextInput::make('max_grade')
                            ->label(__('app.max_grade'))
                            ->helperText(__('app.max_grade_help'))
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(100)
                            ->suffix('/20')
                            ->default(20)
                            ->required(),
                    ])->columns(3),
                
                Forms\Components\Section::make(__('app.academic_structure'))
                    ->description(__('app.academic_structure_desc'))
                    ->schema([
                        Forms\Components\Select::make('terms_per_year')
                            ->label(__('app.terms_per_year'))
                            ->options([
                                '1' => __('app.term_annual'),
                                '2' => __('app.terms_semesters'),
                                '3' => __('app.terms_trimesters'),
                                '4' => __('app.terms_quarters'),
                            ])
                            ->required(),
                    ])->columns(2),
                
                Forms\Components\Section::make(__('app.attendance_policies'))
                    ->description(__('app.attendance_policies_desc'))
                    // Policy settings are children of the module flag (opt-in).
                    ->visible(fn () => feature('attendance'))
                    ->schema([
                        Forms\Components\Toggle::make('attendance_required')
                            ->label(__('app.attendance_required_label'))
                            ->helperText(__('app.attendance_required_help')),
                        Forms\Components\TextInput::make('min_attendance_percentage')
                            ->label(__('app.min_attendance_percentage'))
                            ->integer()
                            ->minValue(0)
                            ->maxValue(100)
                            ->suffix('%')
                            ->required(),
                        Forms\Components\TextInput::make('max_absences_per_term')
                            ->label(__('app.max_absences_per_term'))
                            ->integer()
                            ->minValue(0)
                            ->maxValue(50)
                            ->required(),
                    ])->columns(2),
                
                Forms\Components\Section::make(__('app.assignment_policies'))
                    ->description(__('app.assignment_policies_desc'))
                    // Policy settings are children of the module flag (opt-in).
                    ->visible(fn () => feature('submissions'))
                    ->schema([
                        Forms\Components\TextInput::make('late_submission_penalty')
                            ->label(__('app.late_submission_penalty'))
                            ->integer()
                            ->minValue(0)
                            ->maxValue(100)
                            ->suffix('%')
                            ->helperText(__('app.late_submission_penalty_help')),
                    ])->columns(2),
                
                Forms\Components\Actions::make([
                    Forms\Components\Actions\Action::make('save')
                        ->label(__('app.save_changes'))
                        ->icon('heroicon-m-check-circle')
                        ->color('primary')
                        ->action(function () {
                            $this->save();
                        }),
                ])
            ])
            ->statePath('data');
    }

    protected function getFormActions(): array
    {
        return [];
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $academicData = [
            'academic.grading_system' => $data['grading_system'],
            'academic.passing_grade' => (int) $data['passing_grade'],
            'academic.max_grade' => (int) $data['max_grade'],
            'academic.terms_per_year' => (int) $data['terms_per_year'],
            'academic.attendance_required' => $data['attendance_required'],
            'academic.min_attendance_percentage' => (int) $data['min_attendance_percentage'],
            'academic.max_absences_per_term' => (int) $data['max_absences_per_term'],
            'academic.late_submission_penalty' => (int) $data['late_submission_penalty'],
        ];

        $this->settingsService->updateAcademicSettings($academicData);

        Notification::make()
            ->title(__('app.academic_settings_saved'))
            ->success()
            ->send();
    }
}