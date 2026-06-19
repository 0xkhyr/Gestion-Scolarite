<?php

namespace App\Filament\Widgets;

use Illuminate\Contracts\Support\Htmlable;
use Filament\Tables\Columns\TextColumn;
use App\Models\Cours;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class DailyScheduleWidget extends BaseWidget
{
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 'full';

    public static function canView(): bool
    {
        return auth()->user()->hasRole(['super_admin', 'admin', 'director', 'academic_coordinator', 'teacher', 'secretary']);
    }

    public function getTableHeading(): string | Htmlable | null
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
        $user = auth()->user();
        
        if ($user->hasRole('teacher')) {
            return __('app.mon_emploi_du_temps') . ' - ' . __("app.$todayKey") . ' (' . Carbon::now()->format('d/m/Y') . ')';
        }
        
        return __('app.emploi_du_temps') . ' - ' . __("app.$todayKey") . ' (' . Carbon::now()->format('d/m/Y') . ')';
    }

    public function table(Table $table): Table
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
        $user = auth()->user();

        return $table
            ->query(function () use ($todayKey, $user) {
                // Today's recurring slots (no date) + any one-off session dated today.
                $query = Cours::query()
                    ->where(function (Builder $q) use ($todayKey) {
                        $q->where(fn (Builder $r) => $r->whereNull('date')->where('jour', $todayKey))
                          ->orWhereDate('date', Carbon::today());
                    })
                    ->with(['classe', 'matiere', 'enseignant']);

                // Teachers see only their courses
                if ($user->hasRole('teacher')) {
                    $enseignant = $user->profile;
                    if ($enseignant) {
                        $query->where('id_enseignant', $enseignant->id_enseignant);
                    } else {
                        $query->whereRaw('1 = 0');
                    }
                }

                return $query->orderBy('date_debut');
            })
            ->columns([
                TextColumn::make('classe.nom_classe')
                    ->label(__('app.classe'))
                    ->formatStateUsing(fn ($record) => $record->classe?->code)
                    ->badge()
                    ->color('info')
                    ->sortable(),
                TextColumn::make('matiere.nom_matiere')
                    ->label(__('app.matiere'))
                    ->formatStateUsing(fn ($record) => $record->matiere
                        ? (__('app.' . $record->matiere->code_matiere) === 'app.' . $record->matiere->code_matiere
                            ? $record->matiere->nom_matiere
                            : __('app.' . $record->matiere->code_matiere))
                        : '—'),
                TextColumn::make('enseignant.nom')
                    ->label(__('app.enseignant'))
                    ->formatStateUsing(fn ($record) => ($record->enseignant->nom ?? '') . ' ' . ($record->enseignant->prenom ?? '')),
                TextColumn::make('date_debut')
                    ->label(__('app.debut'))
                    ->time('H:i'),
                TextColumn::make('date_fin')
                    ->label(__('app.fin'))
                    ->time('H:i'),
            ])
            ->paginated(false);
    }
}
