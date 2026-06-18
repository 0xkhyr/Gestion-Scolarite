<?php

namespace App\Filament\Teacher\Widgets;

use Filament\Tables\Columns\TextColumn;
use App\Models\Note;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class TeacherRecentNotes extends BaseWidget
{
    protected static ?int $sort = 6;
    protected int | string | array $columnSpan = 'full';

    public static function canView(): bool
    {
        return auth()->user()->hasRole('teacher') || auth()->user()->hasRole('enseignant');
    }

    public function getTableHeading(): string
    {
        return __('app.my_recent_grades');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(function () {
                $user = auth()->user();
                $enseignant = $user->profile;
                
                if (!$enseignant) {
                    return Note::query()->whereRaw('1 = 0');
                }
                
                // Get notes for students in teacher's classes
                $teacherClasses = $enseignant->classes()->pluck('classes.id_classe');
                
                return Note::query()
                    ->whereHas('etudiant', function (Builder $q) use ($teacherClasses) {
                        $q->whereIn('id_classe', $teacherClasses);
                    })
                    ->with(['etudiant', 'evaluation', 'matiere'])
                    ->latest()
                    ->limit(10);
            })
            ->columns([
                TextColumn::make('etudiant.nom')
                    ->label(__('app.etudiant'))
                    ->formatStateUsing(fn ($record) => "{$record->etudiant->nom} {$record->etudiant->prenom}")
                    ->searchable(),
                    
                TextColumn::make('evaluation.titre')
                    ->label(__('app.evaluation'))
                    ->limit(30),
                    
                TextColumn::make('note')
                    ->label(__('app.note'))
                    ->badge()
                    ->color(function ($state, $record) {
                        $max = $record->evaluation->note_max ?? 20;
                        $percent = ($state / $max) * 100;
                        if ($percent >= 75) return 'success';
                        if ($percent >= 50) return 'warning';
                        return 'danger';
                    })
                    ->formatStateUsing(function ($state, $record) {
                        $max = $record->evaluation->note_max ?? 20;
                        return "{$state}/{$max}";
                    }),
                    
                TextColumn::make('created_at')
                    ->label(__('app.entered_on'))
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated(false);
    }
}