<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\HasRoleBasedAccess;
use App\Filament\Resources\RoleResource\Pages;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class RoleResource extends Resource
{
    use HasRoleBasedAccess;
    
    protected static ?string $model = Role::class;

    protected static ?string $navigationIcon = 'heroicon-o-key';
    
    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string
    {
        return __('app.systeme');
    }

    public static function getNavigationLabel(): string
    {
        return __('app.roles');
    }

    public static function getPluralLabel(): string
    {
        return __('app.roles');
    }

    public static function getModelLabel(): string
    {
        return __('app.role');
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->hasPermissionTo('role.manage');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->hasPermissionTo('role.manage');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->hasPermissionTo('role.manage');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->hasPermissionTo('role.manage');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('app.role_information'))
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label(__('app.role_name'))
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                            
                        Forms\Components\Select::make('permissions')
                            ->label(__('app.permissions'))
                            ->multiple()
                            ->relationship('permissions', 'name')
                            ->preload()
                            ->searchable(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('app.role_name'))
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('primary'),
                    
                Tables\Columns\TextColumn::make('permissions_count')
                    ->label(__('app.permissions'))
                    ->counts('permissions')
                    ->badge()
                    ->color('success'),
                    
                Tables\Columns\TextColumn::make('users_count')
                    ->label(__('app.users'))
                    ->counts('users')
                    ->badge()
                    ->color('info'),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('app.created'))
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn ($record) => !in_array($record->name, ['super_admin', 'admin', 'teacher', 'enseignant', 'student', 'etudiant'])),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->action(function (Tables\Actions\DeleteBulkAction $action) {
                            $action->getRecords()->each(function ($record) {
                                if (!in_array($record->name, ['super_admin', 'admin', 'teacher', 'enseignant', 'student', 'etudiant'])) {
                                    $record->delete();
                                }
                            });
                        }),
                ]),
            ])
            ->defaultSort('name', 'asc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'edit' => Pages\EditRole::route('/{record}/edit'),
        ];
    }
}
