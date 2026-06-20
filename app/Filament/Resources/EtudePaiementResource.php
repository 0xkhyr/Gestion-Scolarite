<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Illuminate\Support\HtmlString;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ExportAction;
use Filament\Actions\ExportBulkAction;
use App\Filament\Exports\EtudePaiementExporter;
use Filament\Actions\BulkAction;
use App\Filament\Resources\EtudePaiementResource\Pages\ListEtudePaiements;
use App\Filament\Resources\EtudePaiementResource\Pages\CreateEtudePaiement;
use App\Filament\Resources\EtudePaiementResource\Pages\EditEtudePaiement;
use App\Filament\Concerns\HasRoleBasedAccess;
use App\Filament\Resources\EtudePaiementResource\Pages;
use App\Filament\Resources\EtudePaiementResource\RelationManagers;
use App\Models\EtudePaiement;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Barryvdh\DomPDF\Facade\Pdf;

class EtudePaiementResource extends Resource
{
    use HasRoleBasedAccess;
    
    protected static ?string $model = EtudePaiement::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-currency-dollar';
    
    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string
    {
        return __('app.gestion_financiere');
    }

    public static function getNavigationLabel(): string
    {
        return __('app.paiements_etudiants');
    }

    public static function getPluralLabel(): string
    {
        return __('app.paiements_etudiants');
    }

    public static function getModelLabel(): string
    {
        return __('app.paiements_etudiants');
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

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('app.payment_information'))
                    ->visible(fn () => auth()->user()->hasPermissionTo('payment.create') || auth()->user()->hasPermissionTo('payment.edit'))
                    ->schema([
                        Select::make('id_etudiant')
                            ->label(__('app.etudiant'))
                            ->relationship('etudiant', 'matricule')
                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->nom} {$record->prenom} ({$record->matricule})")
                            ->required()
                            ->searchable(['matricule'])
                            ->preload(),
                            
                        Select::make('typepaye')
                            ->label(__('app.type_paiement'))
                            ->required()
                            ->options([
                                'scolarite' => __('app.tuition'),
                                'inscription' => __('app.enrollment'),
                                'examen' => __('app.examen'),
                                'uniforme' => __('app.uniform'),
                                'transport' => __('app.transport'),
                                'cantine' => __('app.cafeteria'),
                                'autre' => __('app.other'),
                            ]),
                            
                        TextInput::make('montant')
                            ->label(__('app.amount'))
                            ->required()
                            ->numeric()
                            ->prefix(config('app.currency', 'MRU'))
                            ->minValue(0)
                            ->default(0.00),
                    ])
                    ->columns(3),
                    
                Section::make(__('app.payment_status'))
                    ->visible(fn () => auth()->user()->hasPermissionTo('payment.create') || auth()->user()->hasPermissionTo('payment.edit'))
                    ->schema([
                        Select::make('statut')
                            ->label(__('app.status'))
                            ->required()
                            ->options([
                                'pending' => __('app.pending'),
                                'paid' => __('app.paye'),
                                'partial' => __('app.partiel'),
                                'cancelled' => __('app.cancelled'),
                            ])
                            ->default('pending'),
                            
                        DatePicker::make('date_paiement')
                            ->label(__('app.payment_date'))
                            ->displayFormat('d/m/Y')
                            ->default(now()),
                    ])
                    ->columns(2),
                    
                // Read-only payment summary for users with view-only permissions
                Section::make(__('app.payment_summary'))
                    ->visible(fn () => auth()->user()->hasPermissionTo('payment.view') && !auth()->user()->hasPermissionTo('payment.create') && !auth()->user()->hasPermissionTo('payment.edit'))
                    ->schema([
                        Placeholder::make('etudiant_info')
                            ->label(__('app.etudiant'))
                            ->content(fn ($record) => $record->etudiant 
                                ? new HtmlString('<strong>' . $record->etudiant->nom . ' ' . $record->etudiant->prenom . '</strong><br><small>' . $record->etudiant->matricule . '</small>')
                                : '-'),
                                
                        Placeholder::make('type_paiement')
                            ->label(__('app.type_paiement'))
                            ->content(fn ($record) => new HtmlString('<span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium bg-blue-50 text-blue-700 ring-1 ring-inset ring-blue-700/10">' . ucfirst($record->typepaye) . '</span>')),
                            
                        Placeholder::make('montant_display')
                            ->label(__('app.amount'))
                            ->content(fn ($record) => new HtmlString('<span class="text-lg font-semibold text-green-600">' . config('app.currency', 'MRU') . ' ' . number_format($record->montant, 2) . '</span>')),
                            
                        Placeholder::make('statut_display')
                            ->label(__('app.status'))
                            ->content(fn ($record) => match($record->statut) {
                                'paid' => new HtmlString('<span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium bg-green-50 text-green-700 ring-1 ring-inset ring-green-700/10">✅ ' . __('app.paye') . '</span>'),
                                'partial' => new HtmlString('<span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium bg-yellow-50 text-yellow-700 ring-1 ring-inset ring-yellow-700/10">⚠️ ' . __('app.partiel') . '</span>'),
                                'pending' => new HtmlString('<span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium bg-blue-50 text-blue-700 ring-1 ring-inset ring-blue-700/10">⏳ ' . __('app.pending') . '</span>'),
                                'cancelled' => new HtmlString('<span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium bg-red-50 text-red-700 ring-1 ring-inset ring-red-700/10">❌ ' . __('app.cancelled') . '</span>'),
                                default => new HtmlString('<span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium bg-gray-50 text-gray-700 ring-1 ring-inset ring-gray-700/10">' . __('app.unknown') . '</span>'),
                            }),
                            
                        Placeholder::make('date_paiement_display')
                            ->label(__('app.payment_date'))
                            ->content(fn ($record) => $record->date_paiement ? $record->date_paiement->format('d/m/Y') : '-'),
                    ])
                    ->columns(2),
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('etudiant.matricule')
                    ->label(__('app.matricule'))
                    ->searchable()
                    ->sortable(),
                    
                TextColumn::make('etudiant.nom')
                    ->label(__('app.etudiant'))
                    ->formatStateUsing(fn ($record) => "{$record->etudiant->nom} {$record->etudiant->prenom}")
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->whereHas('etudiant', function (Builder $query) use ($search) {
                            $query->where('nom', 'like', "%{$search}%")
                                ->orWhere('prenom', 'like', "%{$search}%");
                        });
                    })
                    ->sortable(),
                    
                TextColumn::make('typepaye')
                    ->label(__('app.type_paiement'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'scolarite' => 'primary',
                        'inscription' => 'success',
                        'examen' => 'warning',
                        'transport' => 'info',
                        default => 'gray',
                    }),
                    
                TextColumn::make('montant')
                    ->label(__('app.amount'))
                    ->money(config('app.currency', 'MRU'),locale: 'en')
                    ->sortable()
                    ->summarize([
                        Sum::make()
                            ->money(config('app.currency', 'MRU'),locale: 'en')
                            ->visible(fn () => auth()->user()->hasRole(['admin', 'super_admin'])),
                    ]),
                    
                TextColumn::make('statut')
                    ->label(__('app.status'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'paid' => 'success',
                        'partial' => 'warning',
                        'pending' => 'info',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),
                    
                TextColumn::make('date_paiement')
                    ->label(__('app.payment_date'))
                    ->date('d/m/Y')
                    ->sortable(),
                    
                TextColumn::make('created_at')
                    ->label(__('app.cree_a'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('id_etudiant')
                    ->label(__('app.etudiant'))
                    ->relationship('etudiant', 'matricule')
                    ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->nom} {$record->prenom} ({$record->matricule})")
                    ->searchable()
                    ->preload(),
                    
                SelectFilter::make('statut')
                    ->label(__('app.status'))
                    ->options([
                        'pending' => __('app.pending'),
                        'paid' => __('app.paid'),
                        'partial' => __('app.partial'),
                        'cancelled' => __('app.cancelled'),
                    ]),
                    
                SelectFilter::make('typepaye')
                    ->label(__('app.type_paiement'))
                    ->options([
                        'scolarite' => __('app.tuition'),
                        'inscription' => __('app.enrollment'),
                        'examen' => __('app.examen'),
                        'uniforme' => __('app.uniform'),
                        'transport' => __('app.transport'),
                        'cantine' => __('app.cafeteria'),
                        'autre' => __('app.other'),
                    ]),
                    
                Filter::make('date_paiement')
                    ->schema([
                        DatePicker::make('from')
                            ->label(__('app.from_date')),
                        DatePicker::make('until')
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
            ->headerActions([
                ExportAction::make()->exporter(EtudePaiementExporter::class),
            ])
            ->recordActions([
                Action::make('printReceipt')
                    ->label(__('app.imprimer_recu'))
                    ->icon('heroicon-o-printer')
                    ->color('info')
                    ->action(function (EtudePaiement $record) {
                        $record->load(['etudiant', 'etudiant.classe']);
                        
                        $pdf = Pdf::loadView('pdf.receipt', [
                            'payment' => $record,
                        ])->setPaper('a5', 'landscape');

                        return response()->streamDownload(function () use ($pdf) {
                            echo $pdf->output();
                        }, __("app.recu_paiement")."_{$record->id_paiements}.pdf");
                    }),
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    ExportBulkAction::make()->exporter(EtudePaiementExporter::class),
                    DeleteBulkAction::make(),
                    BulkAction::make('markAsPaid')
                        ->label(__('app.mark_as_paid'))
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(fn ($records) => $records->each->update(['statut' => 'paid']))
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
            'index' => ListEtudePaiements::route('/'),
            'create' => CreateEtudePaiement::route('/create'),
            'edit' => EditEtudePaiement::route('/{record}/edit'),
        ];
    }
}
