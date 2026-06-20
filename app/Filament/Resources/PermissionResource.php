<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\PermissionResource\Pages\ListPermissions;
use App\Filament\Resources\PermissionResource\Pages\CreatePermission;
use App\Filament\Resources\PermissionResource\Pages\EditPermission;
use App\Filament\Concerns\HasRoleBasedAccess;
use App\Filament\Resources\PermissionResource\Pages;
use App\Filament\Resources\PermissionResource\RelationManagers;
use Spatie\Permission\Models\Permission;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PermissionResource extends Resource
{
    use HasRoleBasedAccess;
    
    protected static ?string $model = Permission::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-lock-closed';

    protected static ?int $navigationSort = 3;

    public static function getNavigationGroup(): ?string
    {
        return __('app.systeme');
    }

    public static function getNavigationLabel(): string
    {
        return __('app.permissions');
    }

    public static function getPluralLabel(): string
    {
        return __('app.permissions');
    }

    public static function getModelLabel(): string
    {
        return __('app.permission');
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->hasPermissionTo('permission.manage');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->hasPermissionTo('permission.manage');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->hasPermissionTo('permission.manage');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->hasPermissionTo('permission.manage');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('app.permission_information'))
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        TextInput::make('guard_name')
                            ->required()
                            ->default('web')
                            ->maxLength(255),
                    ]),
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->badge(),
                TextColumn::make('guard_name')
                    ->badge()
                    ->color('gray'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPermissions::route('/'),
            'create' => CreatePermission::route('/create'),
            'edit' => EditPermission::route('/{record}/edit'),
        ];
    }
}
