<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PageResource\Pages;
use App\Models\Page;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Database\Eloquent\Collection;
use App\Filament\Concerns\HasRoleBasedAccess;

class PageResource extends Resource
{

    use HasRoleBasedAccess;

    protected static ?string $model = Page::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    
    protected static ?int $navigationSort = 5;

    public static function getNavigationGroup(): ?string
    {
        return __('app.systeme');
    }

    public static function getNavigationLabel(): string
    {
        return __('app.pages');
    }

    public static function getPluralLabel(): string
    {
        return __('app.pages');
    }

    public static function getModelLabel(): string
    {
        return __('app.page');
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->hasPermissionTo('page.manage') || auth()->user()->hasRole('super_admin');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->hasPermissionTo('page.manage') || auth()->user()->hasRole('super_admin');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->hasPermissionTo('page.manage') || auth()->user()->hasRole('super_admin');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->hasPermissionTo('page.manage') || auth()->user()->hasRole('super_admin');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('app.page_information'))
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label(__('app.title'))
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (string $context, $state, Forms\Set $set) => $context === 'create' ? $set('slug', \Illuminate\Support\Str::slug($state)) : null),

                        Forms\Components\TextInput::make('slug')
                            ->label(__('app.slug'))
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->rules(['alpha_dash'])
                            ->helperText(__('app.slug_helper')),

                        Forms\Components\Select::make('status')
                            ->label(__('app.status'))
                            ->options([
                                'draft' => __('app.draft'),
                                'published' => __('app.published'),
                                'scheduled' => __('app.scheduled'),
                            ])
                            ->default('draft')
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                if ($state === 'published' && !$set('published_at', null)) {
                                    $set('published_at', now());
                                }
                            }),

                        Forms\Components\DateTimePicker::make('published_at')
                            ->label(__('app.publish_date'))
                            ->helperText(__('app.publish_date_helper'))
                            ->visible(fn (Forms\Get $get) => in_array($get('status'), ['published', 'scheduled']))
                            ->required(fn (Forms\Get $get) => $get('status') === 'scheduled'),

                        Forms\Components\Radio::make('editor_mode')
                            ->label(__('Content Editor Mode'))
                            ->options([
                                'visual' => __('Visual Editor (WYSIWYG - for non-technical users)'),
                                'html' => __('HTML Code Editor (Full control with Tailwind CSS)'),
                            ])
                            ->default('visual')
                            ->inline()
                            ->live()
                            ->afterStateHydrated(function (Forms\Components\Radio $component, $state) {
                                if (!$state) {
                                    $component->state('visual');
                                }
                            })
                            ->columnSpanFull(),

                        Forms\Components\RichEditor::make('content')
                            ->label(__('app.content'))
                            ->toolbarButtons([
                                'blockquote',
                                'bold',
                                'bulletList',
                                'codeBlock',
                                'h2',
                                'h3',
                                'h4',
                                'italic',
                                'link',
                                'orderedList',
                                'redo',
                                'strike',
                                'table',
                                'underline',
                                'undo',
                            ])
                            ->visible(fn (Forms\Get $get) => $get('editor_mode') === 'visual')
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('content')
                            ->label(__('HTML Content'))
                            ->rows(20)
                            ->helperText(__('Full HTML supported. Use Tailwind CSS classes for styling. Example: <div class="bg-blue-500 p-4 rounded-lg">Content</div>'))
                            ->placeholder('<div class="container mx-auto px-4">
    <h1 class="text-3xl font-bold text-gray-800 mb-4">Page Title</h1>
    <p class="text-gray-600">Your content here...</p>
</div>')
                            ->visible(fn (Forms\Get $get) => $get('editor_mode') === 'html')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make(__('app.seo_settings'))
                    ->schema([
                        Forms\Components\TextInput::make('meta_title')
                            ->label(__('app.meta_title'))
                            ->maxLength(60)
                            ->helperText(__('app.meta_title_helper'))
                            ->placeholder(fn (Forms\Get $get) => $get('title')),

                        Forms\Components\Textarea::make('meta_description')
                            ->label(__('app.meta_description'))
                            ->maxLength(160)
                            ->rows(3)
                            ->helperText(__('app.meta_description_helper')),

                        Forms\Components\TextInput::make('meta_keywords')
                            ->label(__('app.meta_keywords'))
                            ->helperText(__('app.meta_keywords_helper'))
                            ->placeholder(__('app.keywords_placeholder')),
                    ])
                    ->columns(1)
                    ->collapsed(),

                Forms\Components\Section::make(__('Homepage Sections'))
                    ->description(__('Content shown on the homepage. Leave a field empty to use the default text.'))
                    ->visible(fn (Forms\Get $get) => $get('slug') === 'homepage')
                    ->schema([
                        Forms\Components\TextInput::make('settings.hero_title')
                            ->label(__('Hero title'))
                            ->maxLength(255),

                        Forms\Components\Textarea::make('settings.hero_subtitle')
                            ->label(__('Hero subtitle'))
                            ->rows(2)
                            ->maxLength(500),

                        Forms\Components\FileUpload::make('settings.hero_image')
                            ->label(__('Hero image'))
                            ->helperText(__('Shown next to the hero text. A neutral placeholder is used when empty.'))
                            ->image()
                            ->directory('pages')
                            ->disk('public'),

                        Forms\Components\Fieldset::make(__('Primary button'))
                            ->schema([
                                Forms\Components\TextInput::make('settings.cta_primary_label')
                                    ->label(__('Label'))
                                    ->maxLength(50),
                                Forms\Components\TextInput::make('settings.cta_primary_url')
                                    ->label(__('URL'))
                                    ->maxLength(255),
                            ]),

                        Forms\Components\Fieldset::make(__('Secondary button'))
                            ->schema([
                                Forms\Components\TextInput::make('settings.cta_secondary_label')
                                    ->label(__('Label'))
                                    ->maxLength(50),
                                Forms\Components\TextInput::make('settings.cta_secondary_url')
                                    ->label(__('URL'))
                                    ->maxLength(255),
                            ]),

                        Forms\Components\TextInput::make('settings.features_heading')
                            ->label(__('Features heading'))
                            ->maxLength(255),

                        Forms\Components\Textarea::make('settings.features_subheading')
                            ->label(__('Features subheading'))
                            ->rows(2)
                            ->maxLength(500),

                        Forms\Components\Repeater::make('settings.features')
                            ->label(__('Features'))
                            ->schema([
                                Forms\Components\TextInput::make('icon')
                                    ->label(__('Icon'))
                                    ->helperText(__('Material icon name, e.g. school, menu_book, insights'))
                                    ->maxLength(50),
                                Forms\Components\TextInput::make('title')
                                    ->label(__('app.title'))
                                    ->required()
                                    ->maxLength(100),
                                Forms\Components\Textarea::make('description')
                                    ->label(__('Description'))
                                    ->rows(2)
                                    ->maxLength(300),
                            ])
                            ->columns(1)
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['title'] ?? null)
                            ->defaultItems(0)
                            ->columnSpanFull(),

                        Forms\Components\Toggle::make('settings.show_portals')
                            ->label(__('Show portals section'))
                            ->default(true),

                        Forms\Components\TextInput::make('settings.portals_heading')
                            ->label(__('Portals heading'))
                            ->maxLength(255)
                            ->visible(fn (Forms\Get $get) => $get('settings.show_portals')),

                        Forms\Components\Textarea::make('settings.portals_subheading')
                            ->label(__('Portals subheading'))
                            ->rows(2)
                            ->maxLength(500)
                            ->visible(fn (Forms\Get $get) => $get('settings.show_portals')),
                    ])
                    ->columns(1)
                    ->collapsed(),

                Forms\Components\Section::make(__('About Page Sections'))
                    ->description(__('Optional content blocks for the about page. Empty blocks are hidden on the site.'))
                    ->visible(fn (Forms\Get $get) => $get('slug') === 'about')
                    ->schema([
                        Forms\Components\TextInput::make('settings.header_title')
                            ->label(__('Header title'))
                            ->maxLength(255),

                        Forms\Components\Textarea::make('settings.header_subtitle')
                            ->label(__('Header subtitle'))
                            ->rows(2)
                            ->maxLength(500),

                        Forms\Components\Repeater::make('settings.highlights')
                            ->label(__('Highlight cards'))
                            ->schema([
                                Forms\Components\TextInput::make('icon')
                                    ->label(__('Icon'))
                                    ->helperText(__('Material icon name, e.g. history, emoji_events'))
                                    ->maxLength(50),
                                Forms\Components\TextInput::make('title')
                                    ->label(__('app.title'))
                                    ->required()
                                    ->maxLength(100),
                                Forms\Components\Textarea::make('description')
                                    ->label(__('Description'))
                                    ->rows(2)
                                    ->maxLength(500),
                            ])
                            ->columns(1)
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['title'] ?? null)
                            ->defaultItems(0)
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('settings.team_heading')
                            ->label(__('Team heading'))
                            ->maxLength(255),

                        Forms\Components\Textarea::make('settings.team_subheading')
                            ->label(__('Team subheading'))
                            ->rows(2)
                            ->maxLength(500),

                        Forms\Components\Repeater::make('settings.team')
                            ->label(__('Team members'))
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label(__('app.name'))
                                    ->required()
                                    ->maxLength(100),
                                Forms\Components\TextInput::make('role')
                                    ->label(__('Role'))
                                    ->maxLength(100),
                                Forms\Components\Textarea::make('bio')
                                    ->label(__('Bio'))
                                    ->rows(2)
                                    ->maxLength(300),
                                Forms\Components\FileUpload::make('photo')
                                    ->label(__('Photo'))
                                    ->image()
                                    ->directory('team')
                                    ->disk('public'),
                            ])
                            ->columns(1)
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['name'] ?? null)
                            ->defaultItems(0)
                            ->columnSpanFull(),

                        Forms\Components\Fieldset::make(__('Call to action'))
                            ->schema([
                                Forms\Components\TextInput::make('settings.cta_heading')
                                    ->label(__('Heading'))
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('settings.cta_label')
                                    ->label(__('Button label'))
                                    ->maxLength(50),
                                Forms\Components\Textarea::make('settings.cta_text')
                                    ->label(__('Text'))
                                    ->rows(2)
                                    ->maxLength(500)
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->columns(1)
                    ->collapsed(),

                Forms\Components\Section::make(__('Contact Page Sections'))
                    ->visible(fn (Forms\Get $get) => $get('slug') === 'contact')
                    ->schema([
                        Forms\Components\TextInput::make('settings.header_title')
                            ->label(__('Header title'))
                            ->maxLength(255),

                        Forms\Components\Textarea::make('settings.header_subtitle')
                            ->label(__('Header subtitle'))
                            ->rows(2)
                            ->maxLength(500),

                        Forms\Components\Textarea::make('settings.form_intro')
                            ->label(__('Form introduction'))
                            ->rows(2)
                            ->maxLength(500),

                        Forms\Components\Repeater::make('settings.office_hours')
                            ->label(__('Office hours'))
                            ->schema([
                                Forms\Components\TextInput::make('label')
                                    ->label(__('Days'))
                                    ->placeholder(__('Monday - Friday'))
                                    ->required()
                                    ->maxLength(100),
                                Forms\Components\TextInput::make('value')
                                    ->label(__('Hours'))
                                    ->placeholder('8:00 - 16:00')
                                    ->required()
                                    ->maxLength(100),
                            ])
                            ->columns(2)
                            ->defaultItems(0)
                            ->columnSpanFull(),
                    ])
                    ->columns(1)
                    ->collapsed(),

                Forms\Components\Section::make(__('app.page_settings'))
                    ->schema([
                        Forms\Components\Toggle::make('is_enabled')
                            ->label(__('app.page_enabled'))
                            ->helperText(__('app.page_enabled_helper'))
                            ->default(true),

                        Forms\Components\Toggle::make('is_public')
                            ->label(__('app.page_public'))
                            ->helperText(__('app.page_public_helper'))
                            ->default(true),

                        Forms\Components\TextInput::make('sort_order')
                            ->label(__('app.sort_order'))
                            ->numeric()
                            ->default(0)
                            ->helperText(__('app.sort_order_helper')),
                    ])
                    ->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label(__('app.title'))
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),

                Tables\Columns\TextColumn::make('slug')
                    ->label(__('app.slug'))
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('status')
                    ->label(__('app.status'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'published' => 'success',
                        'scheduled' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => __('app.' . $state))
                    ->sortable(),

                Tables\Columns\TextColumn::make('published_at')
                    ->label(__('app.published'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\IconColumn::make('is_enabled')
                    ->label(__('app.enabled'))
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_public')
                    ->label(__('app.public'))
                    ->boolean()
                    ->trueIcon('heroicon-o-globe-alt')
                    ->falseIcon('heroicon-o-eye-slash')
                    ->trueColor('info')
                    ->falseColor('warning')
                    ->sortable(),

                Tables\Columns\TextColumn::make('sort_order')
                    ->label(__('app.order'))
                    ->numeric()
                    ->sortable()
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('app.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('app.updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label(__('app.status'))
                    ->options([
                        'draft' => __('app.draft'),
                        'published' => __('app.published'),
                        'scheduled' => __('app.scheduled'),
                    ]),

                Tables\Filters\TernaryFilter::make('is_enabled')
                    ->label(__('app.enabled'))
                    ->placeholder(__('app.all'))
                    ->trueLabel(__('app.enabled_only'))
                    ->falseLabel(__('app.disabled_only')),

                Tables\Filters\TernaryFilter::make('is_public')
                    ->label(__('app.public'))
                    ->placeholder(__('app.all'))
                    ->trueLabel(__('app.public_only'))
                    ->falseLabel(__('app.private_only')),
            ])
            ->actions([
                Tables\Actions\Action::make('preview')
                    ->label(__('app.preview'))
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->url(fn (Page $record): string => route('page.show', ['slug' => $record->slug, 'preview' => true]))
                    ->openUrlInNewTab(),

                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                
                Tables\Actions\Action::make('view_page')
                    ->label(__('app.view_live'))
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->color('success')
                    ->url(fn (Page $record): string => route('page.show', $record->slug))
                    ->openUrlInNewTab()
                    ->visible(fn (Page $record): bool => $record->isPublished() && $record->is_enabled && $record->is_public),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    
                    BulkAction::make('publish')
                        ->label(__('app.publish'))
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(fn (Collection $records) => $records->each->update([
                            'status' => 'published',
                            'published_at' => now(),
                        ]))
                        ->deselectRecordsAfterCompletion(),

                    BulkAction::make('draft')
                        ->label(__('app.move_to_draft'))
                        ->icon('heroicon-o-document')
                        ->color('gray')
                        ->action(fn (Collection $records) => $records->each->update(['status' => 'draft']))
                        ->deselectRecordsAfterCompletion(),
                    
                    BulkAction::make('enable')
                        ->label(__('app.enable'))
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(fn (Collection $records) => $records->each->update(['is_enabled' => true]))
                        ->deselectRecordsAfterCompletion(),

                    BulkAction::make('disable')
                        ->label(__('app.disable'))
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->action(fn (Collection $records) => $records->each->update(['is_enabled' => false]))
                        ->deselectRecordsAfterCompletion(),

                    BulkAction::make('make_public')
                        ->label(__('app.make_public'))
                        ->icon('heroicon-o-globe-alt')
                        ->color('info')
                        ->action(fn (Collection $records) => $records->each->update(['is_public' => true]))
                        ->deselectRecordsAfterCompletion(),

                    BulkAction::make('make_private')
                        ->label(__('app.make_private'))
                        ->icon('heroicon-o-eye-slash')
                        ->color('warning')
                        ->action(fn (Collection $records) => $records->each->update(['is_public' => false]))
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->defaultSort('sort_order', 'asc');
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
            'index' => Pages\ListPages::route('/'),
            'create' => Pages\CreatePage::route('/create'),
            'view' => Pages\ViewPage::route('/{record}'),
            'edit' => Pages\EditPage::route('/{record}/edit'),
        ];
    }
}