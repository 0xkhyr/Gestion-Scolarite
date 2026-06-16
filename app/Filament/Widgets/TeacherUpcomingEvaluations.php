<?php

namespace App\Filament\Widgets;

use App\Models\Evaluation;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Carbon\Carbon;

class TeacherUpcomingEvaluations extends BaseWidget
{
    protected static ?int $sort = 9;
    protected int | string | array $columnSpan = 2;

    public static function canView(): bool
    {
        return auth()->user()->hasRole('teacher');
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
                Tables\Columns\TextColumn::make('titre')
                    ->label(__('app.evaluation'))
                    ->limit(30)
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('type')
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
                    
                Tables\Columns\TextColumn::make('date')
                    ->label(__('app.date'))
                    ->date('d/m/Y')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('classe.nom_classe')
                    ->label(__('app.classe'))
                    ->badge()
                    ->color('info'),
                    
                Tables\Columns\TextColumn::make('note_max')
                    ->label(__('app.note_max'))
                    ->suffix('/20'),
            ])
            ->defaultSort('date', 'asc')
            ->paginated(false)
            ->emptyStateHeading(__('app.aucune_evaluation_a_venir'))
            ->emptyStateDescription(__('app.no_evaluation_scheduled'));
    }
}