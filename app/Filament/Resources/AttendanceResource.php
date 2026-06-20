<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\AttendanceResource\Pages\ListAttendances;
use App\Filament\Resources\AttendanceResource\Pages\EditAttendance;
use App\Filament\Concerns\HasRoleBasedAccess;
use App\Filament\Resources\AttendanceResource\Pages;
use App\Models\Attendance;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class AttendanceResource extends Resource
{
    use HasRoleBasedAccess;

    protected static ?string $model = Attendance::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-clipboard-document-list';

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

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('app.attendance_record'))
                    ->schema([
                        Placeholder::make('etudiant')
                            ->label(__('app.etudiant'))
                            ->content(fn (?Attendance $record) => $record?->etudiant
                                ? trim($record->etudiant->prenom . ' ' . $record->etudiant->nom) . ' (' . $record->etudiant->matricule . ')'
                                : '—'),
                        Placeholder::make('date')
                            ->label(__('app.date'))
                            ->content(fn (?Attendance $record) => $record?->date?->translatedFormat('d M Y') ?? '—'),
                        Select::make('status')
                            ->label(__('app.status'))
                            ->options([
                                'present' => __('app.present'),
                                'absent' => __('app.absent'),
                            ])
                            ->required(),
                    ])->columns(3),
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('etudiant.nom')
                    ->label(__('app.etudiant'))
                    ->formatStateUsing(fn ($record) => trim($record->etudiant?->prenom . ' ' . $record->etudiant?->nom))
                    ->searchable(['nom', 'prenom'])
                    ->sortable(),

                TextColumn::make('classe.nom_classe')
                    ->label(__('app.classe'))
                    ->formatStateUsing(fn ($record) => $record->classe?->label)
                    ->badge()
                    ->color('info')
                    ->sortable(),

                TextColumn::make('cours')
                    ->label(__('app.cours'))
                    ->formatStateUsing(fn ($record) => $record->cours
                        ? ucfirst($record->cours->jour) . ' · ' . ($record->cours->matiere?->nom_matiere ?? '—')
                        : '—'),

                TextColumn::make('date')
                    ->label(__('app.date'))
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('status')
                    ->label(__('app.status'))
                    ->badge()
                    ->formatStateUsing(fn ($state) => __('app.' . $state))
                    ->color(fn ($state) => $state === 'present' ? 'success' : 'danger')
                    ->sortable(),

                TextColumn::make('markedBy.name')
                    ->label(__('app.marked_by'))
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label(__('app.status'))
                    ->options([
                        'present' => __('app.present'),
                        'absent' => __('app.absent'),
                    ]),
                SelectFilter::make('id_classe')
                    ->label(__('app.classe'))
                    ->relationship('classe', 'nom_classe')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->label),
                Filter::make('date')
                    ->schema([
                        DatePicker::make('from')->label(__('app.from')),
                        DatePicker::make('until')->label(__('app.until')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['from'] ?? null, fn ($q, $d) => $q->whereDate('date', '>=', $d))
                            ->when($data['until'] ?? null, fn ($q, $d) => $q->whereDate('date', '<=', $d));
                    }),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('date', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAttendances::route('/'),
            'edit' => EditAttendance::route('/{record}/edit'),
        ];
    }
}
