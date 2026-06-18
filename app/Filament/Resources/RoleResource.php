<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\RoleResource\Pages\ListRoles;
use App\Filament\Resources\RoleResource\Pages\CreateRole;
use App\Filament\Resources\RoleResource\Pages\EditRole;
use App\Filament\Concerns\HasRoleBasedAccess;
use App\Filament\Resources\RoleResource\Pages;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class RoleResource extends Resource
{
    use HasRoleBasedAccess;
    
    protected static ?string $model = Role::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-key';
    
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

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('app.role_information'))
                    ->schema([
                        TextInput::make('name')
                            ->label(__('app.role_name'))
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                            
                        Select::make('permissions')
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
                TextColumn::make('name')
                    ->label(__('app.role_name'))
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('primary'),
                    
                TextColumn::make('permissions_count')
                    ->label(__('app.permissions'))
                    ->counts('permissions')
                    ->badge()
                    ->color('success'),
                    
                TextColumn::make('users_count')
                    ->label(__('app.users'))
                    ->counts('users')
                    ->badge()
                    ->color('info'),
                    
                TextColumn::make('created_at')
                    ->label(__('app.created'))
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make()
                    ->visible(fn ($record) => !in_array($record->name, ['super_admin', 'admin', 'teacher', 'enseignant', 'student', 'etudiant'])),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->action(function (DeleteBulkAction $action) {
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
            'index' => ListRoles::route('/'),
            'create' => CreateRole::route('/create'),
            'edit' => EditRole::route('/{record}/edit'),
        ];
    }
}
