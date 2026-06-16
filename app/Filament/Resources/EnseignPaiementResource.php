<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\HasRoleBasedAccess;
use App\Filament\Resources\EnseignPaiementResource\Pages;
use App\Filament\Resources\EnseignPaiementResource\RelationManagers;
use App\Models\EnseignPaiement;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Barryvdh\DomPDF\Facade\Pdf;

class EnseignPaiementResource extends Resource
{
    use HasRoleBasedAccess;
    
    protected static ?string $model = EnseignPaiement::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    
    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): ?string
    {
        return __('app.gestion_financiere');
    }

    public static function getNavigationLabel(): string
    {
        return __('app.paiements_enseignants');
    }

    public static function getPluralLabel(): string
    {
        return __('app.paiements_enseignants');
    }

    public static function getModelLabel(): string
    {
        return __('app.paiements_enseignants');
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->hasPermissionTo('payment.view');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->hasPermissionTo('payment.create');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->hasPermissionTo('payment.edit');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->hasPermissionTo('payment.delete');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('app.payment_information'))
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label(__('app.enseignant'))
                            ->relationship('enseignant', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                            
                        Forms\Components\Select::make('typepaiement')
                            ->label(__('app.type_paiement'))
                            ->required()
                            ->options([
                                'salaire' => __('app.salary'),
                                'prime' => __('app.bonus'),
                                'avance' => __('app.advance'),
                                'autre' => __('app.other'),
                            ]),
                            
                        Forms\Components\TextInput::make('montant')
                            ->label(__('app.amount'))
                            ->required()
                            ->numeric()
                            ->prefix(config('app.currency', 'MRU') . ' ')
                            ->minValue(0)
                            ->default(0.00),
                    ])
                    ->columns(3),
                    
                Forms\Components\Section::make(__('app.payment_status'))
                    ->schema([
                        Forms\Components\Select::make('statut')
                            ->label(__('app.status'))
                            ->required()
                            ->options([
                                'non_paye' => __('app.pending'),
                                'paye' => __('app.paye'), 
                                'partiel' => __('app.partiel'),
                            ])
                            ->default('non_paye'),
                            
                        Forms\Components\DatePicker::make('date_paiement')
                            ->label(__('app.payment_date'))
                            ->displayFormat('d/m/Y')
                            ->default(now()),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('enseignant.name')
                    ->label(__('app.enseignant'))
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('typepaiement')
                    ->label(__('app.type_paiement'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'salaire' => 'success',
                        'prime' => 'warning',
                        'avance' => 'info',
                        default => 'gray',
                    }),
                    
                Tables\Columns\TextColumn::make('montant')
                    ->label(__('app.amount'))
                    ->money(config('app.currency', 'MRU'),locale: 'en')
                    ->sortable()
                    ->summarize([
                        Tables\Columns\Summarizers\Sum::make()
                            ->money(config('app.currency', 'MRU'),locale: 'en')
                            ->visible(fn () => auth()->user()->hasRole(['admin', 'super_admin'])),
                    ]),
                    
                Tables\Columns\TextColumn::make('statut')
                    ->label(__('app.status'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'paye' => 'success',
                        'non_paye' => 'warning',
                        'partiel' => 'danger',
                        default => 'gray',
                    }),
                    
                Tables\Columns\TextColumn::make('date_paiement')
                    ->label(__('app.payment_date'))
                    ->date('d/m/Y')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('app.cree_a'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('statut')
                    ->label(__('app.status'))
                    ->options([
                        'non_paye' => __('app.pending'),
                        'paye' => __('app.paye'),
                        'partiel' => __('app.partiel'),
                    ]),
                    
                Tables\Filters\SelectFilter::make('typepaiement')
                    ->label(__('app.type_paiement'))
                    ->options([
                        'salaire' => __('app.salary'),
                        'prime' => __('app.bonus'),
                        'avance' => __('app.advance'),
                        'autre' => __('app.other'),
                    ]),
                    
                Tables\Filters\Filter::make('date_paiement')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label(__('app.from_date')),
                        Forms\Components\DatePicker::make('until')
                            ->label(__('app.until_date')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('date_paiement', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('date_paiement', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('printVoucher')
                    ->label(__('app.imprimer_recu'))
                    ->icon('heroicon-o-printer')
                    ->color('info')
                    ->action(function (EnseignPaiement $record) {
                        $record->load(['enseignant']);
                        
                        $pdf = Pdf::loadView('pdf.voucher', [
                            'payment' => $record,
                        ])->setPaper('a4', 'portrait');

                        return response()->streamDownload(function () use ($pdf) {
                            echo $pdf->output();
                        }, "bordereau_paiement_{$record->id_paiements}.pdf");
                    }),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('markAsPaid')
                        ->label(__('app.mark_as_paid'))
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(fn ($records) => $records->each->update(['statut' => 'paye']))
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->defaultSort('date_paiement', 'desc');
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
            'index' => Pages\ListEnseignPaiements::route('/'),
            'create' => Pages\CreateEnseignPaiement::route('/create'),
            'edit' => Pages\EditEnseignPaiement::route('/{record}/edit'),
        ];
    }
}
