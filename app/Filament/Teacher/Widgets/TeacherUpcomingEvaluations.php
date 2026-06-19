<?php

namespace App\Filament\Teacher\Widgets;

use Filament\Tables\Columns\TextColumn;
use App\Models\Evaluation;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Carbon\Carbon;

class TeacherUpcomingEvaluations extends BaseWidget
{
    protected static ?int $sort = 999;
    protected int | string | array $columnSpan = 2; // Match dashboard's 2-column layout

    public static function canView(): bool
    {
        return auth()->user()->hasRole('teacher') || auth()->user()->hasRole('enseignant');
    }

    public function getTableHeading(): string
    {
        return __('app.my_upcoming_evaluations');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(function () {
                $user = auth()->user();
                $enseignant = $user->profile;
                
                if (!$enseignant) {
                    return Evaluation::query()->whereRaw('1 = 0');
                }
                
                // Get teacher's classes
                $teacherClasses = $enseignant->classes()->pluck('classes.id_classe');
                
                return Evaluation::query()
                    ->whereIn('id_classe', $teacherClasses)
                    ->where('date', '>=', Carbon::now()->startOfDay())
                    ->with(['classe', 'matiere'])
                    ->orderBy('date');
            })
            ->columns([
                TextColumn::make('titre')
                    ->label(__('app.evaluation'))
                    ->limit(30)
                    ->searchable(),
                    
                TextColumn::make('type')
                    ->label(__('app.type'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'examen' => 'danger',
                        'controle' => 'warning', 
                        'interrogation' => 'info',
                        'devoir' => 'success',
                        'projet' => 'primary',
                        default => 'gray',
                    }),
                    
                TextColumn::make('date')
                    ->label(__('app.date'))
                    ->date('d/m/Y')
                    ->sortable(),
                    
                TextColumn::make('classe.nom_classe')
                    ->label(__('app.classe'))
                    ->formatStateUsing(fn ($record) => $record->classe?->label)
                    ->badge()
                    ->color('info'),

                TextColumn::make('note_max')
                    ->label(__('app.note_max'))
                    ->suffix('/20'),
            ])
            ->defaultSort('date', 'asc')
            ->paginated(false)
            ->emptyStateHeading(__('app.aucune_evaluation_a_venir'))
            ->emptyStateDescription(__('app.no_evaluation_scheduled'));
    }
}