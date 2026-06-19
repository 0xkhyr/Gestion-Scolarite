<?php

namespace App\Filament\Teacher\Widgets;

use Filament\Tables\Columns\TextColumn;
use App\Models\Cours;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Carbon\Carbon;

class TeacherTodaySchedule extends BaseWidget
{
    protected static ?int $sort = 7;
    protected int | string | array $columnSpan = 'full';

    public static function canView(): bool
    {
        return auth()->user()->hasRole('teacher') || auth()->user()->hasRole('enseignant');
    }

    public function getTableHeading(): string
    {
        $dayMap = [
            'Monday' => 'lundi',
            'Tuesday' => 'mardi', 
            'Wednesday' => 'mercredi',
            'Thursday' => 'jeudi',
            'Friday' => 'vendredi',
            'Saturday' => 'samedi',
            'Sunday' => 'dimanche',
        ];
        
        $todayKey = $dayMap[Carbon::now()->format('l')];
        return __('app.my_courses_today') . ' - ' . __("app.$todayKey") . ' (' . Carbon::now()->format('d/m/Y') . ')';
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(function () {
                $user = auth()->user();
                $enseignant = $user->profile;
                
                if (!$enseignant) {
                    return Cours::query()->whereRaw('1 = 0');
                }
                
                $dayMap = [
                    'Monday' => 'lundi',
                    'Tuesday' => 'mardi',
                    'Wednesday' => 'mercredi', 
                    'Thursday' => 'jeudi',
                    'Friday' => 'vendredi',
                    'Saturday' => 'samedi',
                    'Sunday' => 'dimanche',
                ];
                
                $todayKey = $dayMap[Carbon::now()->format('l')];
                
                return Cours::query()
                    ->where('id_enseignant', $enseignant->id_enseignant)
                    ->where('jour', $todayKey)
                    ->with(['classe', 'matiere'])
                    ->orderBy('date_debut');
            })
            ->columns([
                TextColumn::make('date_debut')
                    ->label(__('app.heure_debut'))
                    ->time('H:i')
                    ->sortable(),
                    
                TextColumn::make('date_fin')
                    ->label(__('app.heure_fin'))
                    ->time('H:i')
                    ->sortable(),
                    
                TextColumn::make('classe.nom_classe')
                    ->label(__('app.classe'))
                    ->formatStateUsing(fn ($record) => $record->classe?->label)
                    ->badge()
                    ->color('info'),

                TextColumn::make('matiere.nom_matiere')
                    ->label(__('app.matiere'))
                    ->badge()
                    ->color('success'),
                    
                TextColumn::make('description')
                    ->label(__('app.description'))
                    ->limit(50),
            ])
            ->defaultSort('date_debut', 'asc')
            ->paginated(false)
            ->emptyStateHeading(__('app.no_courses_today'))
            ->emptyStateDescription(__('app.no_courses_scheduled_today'));
    }
}