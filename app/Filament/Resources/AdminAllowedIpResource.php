<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\AdminAllowedIpResource\Pages\ListAdminAllowedIps;
use App\Filament\Resources\AdminAllowedIpResource\Pages\CreateAdminAllowedIp;
use App\Filament\Resources\AdminAllowedIpResource\Pages\EditAdminAllowedIp;
use App\Filament\Concerns\HasRoleBasedAccess;
use App\Filament\Resources\AdminAllowedIpResource\Pages;
use App\Models\AdminAllowedIp;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AdminAllowedIpResource extends Resource
{
    use HasRoleBasedAccess;
    
    protected static ?string $model = AdminAllowedIp::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-finger-print';

    protected static ?int $navigationSort = 5;

    public static function getNavigationGroup(): ?string
    {
        return __('app.systeme');
    }

    public static function getNavigationLabel(): string
    {
        return __('app.ip_whitelist');
    }

    public static function getPluralLabel(): string
    {
        return __('app.ip_whitelist');
    }

    public static function getModelLabel(): string
    {
        return __('app.ip_autorisee');
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->hasPermissionTo('system.manage_settings');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->hasPermissionTo('system.manage_settings');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->hasPermissionTo('system.manage_settings');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->hasPermissionTo('system.manage_settings');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('app.ip_autorisee'))
                    ->schema([
                        TextInput::make('ip_address')
                            ->label(__('app.ip_address'))
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->placeholder('127.0.0.1')
                            ->ipv4() // Or ipv6() depending on needs, ipv4 is safer default validation
                            ->maxLength(45),
                        
                        Textarea::make('description')
                            ->label(__('app.description'))
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('ip_address')
                    ->label(__('app.ip_address'))
                    ->searchable()
                    ->sortable()
                    ->copyable(),
                    
                TextColumn::make('description')
                    ->label(__('app.description'))
                    ->limit(50)
                    ->tooltip(function (AdminAllowedIp $record): ?string {
                        return $record->description;
                    }),
                    
                TextColumn::make('created_at')
                    ->label(__('app.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
                    
                TextColumn::make('updated_at')
                    ->label(__('app.updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
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
            'index' => ListAdminAllowedIps::route('/'),
            'create' => CreateAdminAllowedIp::route('/create'),
            'edit' => EditAdminAllowedIp::route('/{record}/edit'),
        ];
    }
}
