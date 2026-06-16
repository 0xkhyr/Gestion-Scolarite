<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\HasRoleBasedAccess;
use App\Filament\Resources\ActivityLogResource\Pages;
use Spatie\Activitylog\Models\Activity as ActivityModel;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;

class ActivityLogResource extends Resource
{
    use HasRoleBasedAccess;
    
    protected static ?string $model = ActivityModel::class;

    protected static ?string $navigationIcon = 'heroicon-o-list-bullet';
    protected static ?int $navigationSort = 4;

    public static function getNavigationGroup(): ?string
    {
        return __('app.systeme');
    }

    public static function getNavigationLabel(): string
    {
        return __('app.activity_logs');
    }

    public static function getPluralLabel(): string
    {
        return __('app.activity_logs');
    }

    public static function getModelLabel(): string
    {
        return __('app.activity_log');
    }

    public static function canViewAny(): bool
    {
        $user = auth()->user();

        if (! $user) {
            return false;
        }

        return $user->hasPermissionTo('activity_log.view');
    }

    public static function canCreate(): bool
    {
        return false; // Activity logs should not be manually created
    }

    public static function canEdit($record): bool
    {
        return false; // Activity logs should not be edited
    }

    public static function canDelete($record): bool
    {
        return false; // Activity logs should not be deleted for audit integrity
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make(__('app.log_details') ?? 'Log Details')
                ->schema([
                    Forms\Components\Placeholder::make('log_name')
                        ->label(__('app.log'))
                        ->content(fn ($record) => $record?->log_name
                            ? new HtmlString('<span class="text-sm text-gray-600">' . e($record->log_name) . '</span>')
                            : null
                        )
                        ->columnSpan(1),

                    Forms\Components\Placeholder::make('event')
                        ->label(__('app.action'))
                        ->content(fn ($record) => $record?->event
                            ? new HtmlString('<span class="inline-flex items-center rounded px-2 py-0.5 text-xs font-medium bg-gray-100 text-gray-800">'
                                . e($record->event)
                                . '</span>')
                            : null
                        )
                        ->columnSpan(1),

                    Forms\Components\Placeholder::make('description')
                        ->label(__('app.description'))
                        ->content(fn ($record) => $record?->description
                            ? new HtmlString('<div class="text-sm text-gray-700">' . e($record->description) . '</div>')
                            : null
                        )
                        ->columnSpanFull(),

                    Forms\Components\Placeholder::make('causer')
                        ->label(__('app.causer'))
                        ->content(function ($record) {
                            if (! $record?->causer_id) {
                                return null;
                            }

                            $name = $record->causer?->name
                                ?? (class_basename($record->causer_type) . " #{$record->causer_id}");

                            return new HtmlString('<div class="text-sm font-medium">' . e($name) . '</div>');
                        })
                        ->columnSpan(1),

                    Forms\Components\Placeholder::make('subject')
                        ->label(__('app.subject'))
                        ->content(fn ($record) => $record?->subject_id
                            ? new HtmlString('<div class="text-sm">' . e(class_basename($record->subject_type) . " #{$record->subject_id}") . '</div>')
                            : null
                        )
                        ->columnSpan(1),

                    Forms\Components\Placeholder::make('properties')
                        ->label(__('app.changes'))
                        ->content(fn ($record) => new HtmlString(
                            '<div class="rounded bg-gray-50 dark:bg-gray-900 p-3 text-xs font-mono">
                                <pre class="whitespace-pre-wrap">' .
                                    e(json_encode($record->properties ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) .
                                '</pre>
                            </div>'
                        ))
                        ->columnSpanFull(),

                    Forms\Components\Placeholder::make('ip')
                        ->label(__('app.ip_address'))
                        ->content(fn ($record) => $record->properties['ip_address'] ?? null)
                        ->columnSpan(1),

                    Forms\Components\Placeholder::make('user_agent')
                        ->label(__('app.user_agent'))
                        ->content(fn ($record) => $record->properties['user_agent'] ?? null)
                        ->columnSpan(1),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('causer.name')
                    ->label(__('app.user'))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('description')
                    ->label(__('app.actions'))
                    ->wrap()
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('subject_type')
                    ->label(__('app.resource'))
                    ->formatStateUsing(fn ($state) => $state ? class_basename($state) : null)
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('subject_id')
                    ->label(__('app.resource_id'))
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('properties->ip_address')
                    ->label(__('app.ip_address'))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('app.time'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('log_name')
                    ->label(__('app.log_name'))
                    ->options(fn () => ActivityModel::query()
                        ->distinct()
                        ->whereNotNull('log_name')
                        ->pluck('log_name', 'log_name')
                        ->filter()
                        ->toArray()
                    ),

                Tables\Filters\SelectFilter::make('event')
                    ->label(__('app.event'))
                    ->options(fn () => ActivityModel::query()
                        ->distinct()
                        ->whereNotNull('event')
                        ->pluck('event', 'event')
                        ->filter()
                        ->toArray()
                    ),

                Tables\Filters\Filter::make('date')
                    ->form([
                        Forms\Components\DatePicker::make('from')->label(__('app.from')),
                        Forms\Components\DatePicker::make('to')->label(__('app.to')),
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (! empty($data['from'])) {
                            $query->whereDate('created_at', '>=', $data['from']);
                        }

                        if (! empty($data['to'])) {
                            $query->whereDate('created_at', '<=', $data['to']);
                        }
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListActivityLogs::route('/'),
            'view' => Pages\ViewActivityLog::route('/{record}'),
        ];
    }
}
