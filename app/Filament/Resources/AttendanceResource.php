<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\HasRoleBasedAccess;
use App\Filament\Resources\AttendanceResource\Pages;
use App\Models\Attendance;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class AttendanceResource extends Resource
{
    use HasRoleBasedAccess;

    protected static ?string $model = Attendance::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?int $navigationSort = 4;

    public static function getNavigationGroup(): ?string
    {
        return __('app.gestion_academique');
    }

    public static function getNavigationLabel(): string
    {
        return __('app.attendance');
    }

    public static function getPluralLabel(): string
    {
        return __('app.attendance');
    }

    public static function getModelLabel(): string
    {
        return __('app.attendance_record');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return feature('attendance') && static::canViewAny();
    }

    public static function canViewAny(): bool
    {
        return feature('attendance')
            && (bool) auth()->user()?->hasAnyRole(['super_admin', 'admin', 'secretary', 'teacher', 'enseignant']);
    }

    public static function canCreate(): bool
    {
        // Records are created through the Take Attendance page, not one-by-one.
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        if (! feature('attendance')) {
            return false;
        }

        $user = auth()->user();

        if ($user->hasAnyRole(['super_admin', 'admin', 'secretary'])) {
            return true;
        }

        // Teachers may correct records for their own classes.
        return static::canTeacherAccessRecord($record);
    }

    public static function canDelete(Model $record): bool
    {
        return feature('attendance') && (bool) auth()->user()?->hasAnyRole(['super_admin', 'admin']);
    }

    public static function getEloquentQuery(): Builder
    {
        return static::applyRoleBasedTableScope(parent::getEloquentQuery(), [
            'classColumn' => 'id_classe',
            'studentScope' => false,
        ]);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('app.attendance_record'))
                    ->schema([
                        Forms\Components\Placeholder::make('etudiant')
                            ->label(__('app.etudiant'))
                            ->content(fn (?Attendance $record) => $record?->etudiant
                                ? trim($record->etudiant->prenom . ' ' . $record->etudiant->nom) . ' (' . $record->etudiant->matricule . ')'
                                : '—'),
                        Forms\Components\Placeholder::make('date')
                            ->label(__('app.date'))
                            ->content(fn (?Attendance $record) => $record?->date?->translatedFormat('d M Y') ?? '—'),
                        Forms\Components\Select::make('status')
                            ->label(__('app.status'))
                            ->options([
                                'present' => __('app.present'),
                                'absent' => __('app.absent'),
                            ])
                            ->required(),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('etudiant.nom')
                    ->label(__('app.etudiant'))
                    ->formatStateUsing(fn ($record) => trim($record->etudiant?->prenom . ' ' . $record->etudiant?->nom))
                    ->searchable(['nom', 'prenom'])
                    ->sortable(),

                Tables\Columns\TextColumn::make('classe.nom_classe')
                    ->label(__('app.classe'))
                    ->badge()
                    ->color('info')
                    ->sortable(),

                Tables\Columns\TextColumn::make('cours')
                    ->label(__('app.cours'))
                    ->formatStateUsing(fn ($record) => $record->cours
                        ? ucfirst($record->cours->jour) . ' · ' . ($record->cours->matiere?->nom_matiere ?? '—')
                        : '—'),

                Tables\Columns\TextColumn::make('date')
                    ->label(__('app.date'))
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label(__('app.status'))
                    ->badge()
                    ->formatStateUsing(fn ($state) => __('app.' . $state))
                    ->color(fn ($state) => $state === 'present' ? 'success' : 'danger')
                    ->sortable(),

                Tables\Columns\TextColumn::make('markedBy.name')
                    ->label(__('app.marked_by'))
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label(__('app.status'))
                    ->options([
                        'present' => __('app.present'),
                        'absent' => __('app.absent'),
                    ]),
                Tables\Filters\SelectFilter::make('id_classe')
                    ->label(__('app.classe'))
                    ->relationship('classe', 'nom_classe'),
                Tables\Filters\Filter::make('date')
                    ->form([
                        Forms\Components\DatePicker::make('from')->label(__('app.from')),
                        Forms\Components\DatePicker::make('until')->label(__('app.until')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['from'] ?? null, fn ($q, $d) => $q->whereDate('date', '>=', $d))
                            ->when($data['until'] ?? null, fn ($q, $d) => $q->whereDate('date', '<=', $d));
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('date', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAttendances::route('/'),
            'edit' => Pages\EditAttendance::route('/{record}/edit'),
        ];
    }
}
