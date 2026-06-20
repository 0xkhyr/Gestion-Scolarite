<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Support\Str;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DateTimePicker;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Fieldset;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\BulkAction;
use App\Filament\Resources\PageResource\Pages\ListPages;
use App\Filament\Resources\PageResource\Pages\CreatePage;
use App\Filament\Resources\PageResource\Pages\ViewPage;
use App\Filament\Resources\PageResource\Pages\EditPage;
use App\Filament\Resources\PageResource\Pages;
use App\Models\Page;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use App\Filament\Concerns\HasRoleBasedAccess;

class PageResource extends Resource
{

    use HasRoleBasedAccess;

    protected static ?string $model = Page::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-document-text';
    
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

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('app.page_information'))
                    ->schema([
                        TextInput::make('title')
                            ->label(__('app.title'))
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (string $context, $state, Set $set) => $context === 'create' ? $set('slug', Str::slug($state)) : null),

                        TextInput::make('slug')
                            ->label(__('app.slug'))
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->rules(['alpha_dash'])
                            ->helperText(__('app.slug_helper')),

                        Select::make('status')
                            ->label(__('app.status'))
                            ->options([
                                'draft' => __('app.draft'),
                                'published' => __('app.published'),
                                'scheduled' => __('app.scheduled'),
                            ])
                            ->default('draft')
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, Set $set) {
                                if ($state === 'published' && !$set('published_at', null)) {
                                    $set('published_at', now());
                                }
                            }),

                        DateTimePicker::make('published_at')
                            ->label(__('app.publish_date'))
                            ->helperText(__('app.publish_date_helper'))
                            ->visible(fn (Get $get) => in_array($get('status'), ['published', 'scheduled']))
                            ->required(fn (Get $get) => $get('status') === 'scheduled'),

                        Radio::make('editor_mode')
                            ->label(__('Content Editor Mode'))
                            ->options([
                                'visual' => __('Visual Editor (WYSIWYG - for non-technical users)'),
                                'html' => __('HTML Code Editor (Full control with Tailwind CSS)'),
                            ])
                            ->default('visual')
                            ->inline()
                            ->live()
                            ->afterStateHydrated(function (Radio $component, $state) {
                                if (!$state) {
                                    $component->state('visual');
                                }
                            })
                            ->columnSpanFull(),

                        RichEditor::make('content')
                            ->label(__('app.content'))
                            ->toolbarButtons([
                                ['bold', 'italic', 'underline', 'strike', 'subscript', 'superscript', 'link'],
                                ['h1', 'h2', 'h3'],
                                ['alignStart', 'alignCenter', 'alignEnd'],
                                ['blockquote', 'codeBlock', 'bulletList', 'orderedList'],
                                ['table', 'attachFiles', 'horizontalRule', 'highlight'],
                                ['undo', 'redo', 'clearFormatting'],
                            ])
                            // Inline image/file uploads land on the public disk so they display on the site.
                            ->fileAttachmentsDisk('public')
                            ->fileAttachmentsDirectory('pages/content')
                            ->fileAttachmentsVisibility('public')
                            ->visible(fn (Get $get) => $get('editor_mode') === 'visual')
                            ->columnSpanFull(),

                        Textarea::make('content')
                            ->label(__('HTML Content'))
                            ->rows(20)
                            ->helperText(__('Full HTML supported. Use Tailwind CSS classes for styling. Example: <div class="bg-blue-500 p-4 rounded-lg">Content</div>'))
                            ->placeholder('<div class="container mx-auto px-4">
    <h1 class="text-3xl font-bold text-gray-800 mb-4">Page Title</h1>
    <p class="text-gray-600">Your content here...</p>
</div>')
                            ->visible(fn (Get $get) => $get('editor_mode') === 'html')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make(__('app.seo_settings'))
                    ->schema([
                        TextInput::make('meta_title')
                            ->label(__('app.meta_title'))
                            ->maxLength(60)
                            ->helperText(__('app.meta_title_helper'))
                            ->placeholder(fn (Get $get) => $get('title')),

                        Textarea::make('meta_description')
                            ->label(__('app.meta_description'))
                            ->maxLength(160)
                            ->rows(3)
                            ->helperText(__('app.meta_description_helper')),

                        TextInput::make('meta_keywords')
                            ->label(__('app.meta_keywords'))
                            ->helperText(__('app.meta_keywords_helper'))
                            ->placeholder(__('app.keywords_placeholder')),
                    ])
                    ->columns(1)
                    ->collapsed(),

                Section::make(__('Homepage Sections'))
                    ->description(__('Content shown on the homepage. Leave a field empty to use the default text.'))
                    ->visible(fn (Get $get) => $get('slug') === 'homepage')
                    ->schema([
                        TextInput::make('settings.hero_title')
                            ->label(__('Hero title'))
                            ->maxLength(255),

                        Textarea::make('settings.hero_subtitle')
                            ->label(__('Hero subtitle'))
                            ->rows(2)
                            ->maxLength(500),

                        FileUpload::make('settings.hero_image')
                            ->label(__('Hero image'))
                            ->helperText(__('Shown next to the hero text. A neutral placeholder is used when empty.'))
                            ->image()
                            ->directory('pages')
                            ->disk('public'),

                        Fieldset::make(__('Primary button'))
                            ->schema([
                                TextInput::make('settings.cta_primary_label')
                                    ->label(__('Label'))
                                    ->maxLength(50),
                                TextInput::make('settings.cta_primary_url')
                                    ->label(__('URL'))
                                    ->maxLength(255),
                            ]),

                        Fieldset::make(__('Secondary button'))
                            ->schema([
                                TextInput::make('settings.cta_secondary_label')
                                    ->label(__('Label'))
                                    ->maxLength(50),
                                TextInput::make('settings.cta_secondary_url')
                                    ->label(__('URL'))
                                    ->maxLength(255),
                            ]),

                        TextInput::make('settings.features_heading')
                            ->label(__('Features heading'))
                            ->maxLength(255),

                        Textarea::make('settings.features_subheading')
                            ->label(__('Features subheading'))
                            ->rows(2)
                            ->maxLength(500),

                        Repeater::make('settings.features')
                            ->label(__('Features'))
                            ->schema([
                                TextInput::make('icon')
                                    ->label(__('Icon'))
                                    ->helperText(__('Material icon name, e.g. school, menu_book, insights'))
                                    ->maxLength(50),
                                TextInput::make('title')
                                    ->label(__('app.title'))
                                    ->required()
                                    ->maxLength(100),
                                Textarea::make('description')
                                    ->label(__('Description'))
                                    ->rows(2)
                                    ->maxLength(300),
                            ])
                            ->columns(1)
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['title'] ?? null)
                            ->defaultItems(0)
                            ->columnSpanFull(),

                        Toggle::make('settings.show_portals')
                            ->label(__('Show portals section'))
                            ->default(true),

                        TextInput::make('settings.portals_heading')
                            ->label(__('Portals heading'))
                            ->maxLength(255)
                            ->visible(fn (Get $get) => $get('settings.show_portals')),

                        Textarea::make('settings.portals_subheading')
                            ->label(__('Portals subheading'))
                            ->rows(2)
                            ->maxLength(500)
                            ->visible(fn (Get $get) => $get('settings.show_portals')),
                    ])
                    ->columns(1)
                    ->collapsed(),

                Section::make(__('About Page Sections'))
                    ->description(__('Optional content blocks for the about page. Empty blocks are hidden on the site.'))
                    ->visible(fn (Get $get) => $get('slug') === 'about')
                    ->schema([
                        TextInput::make('settings.header_title')
                            ->label(__('Header title'))
                            ->maxLength(255),

                        Textarea::make('settings.header_subtitle')
                            ->label(__('Header subtitle'))
                            ->rows(2)
                            ->maxLength(500),

                        Repeater::make('settings.highlights')
                            ->label(__('Highlight cards'))
                            ->schema([
                                TextInput::make('icon')
                                    ->label(__('Icon'))
                                    ->helperText(__('Material icon name, e.g. history, emoji_events'))
                                    ->maxLength(50),
                                TextInput::make('title')
                                    ->label(__('app.title'))
                                    ->required()
                                    ->maxLength(100),
                                Textarea::make('description')
                                    ->label(__('Description'))
                                    ->rows(2)
                                    ->maxLength(500),
                            ])
                            ->columns(1)
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['title'] ?? null)
                            ->defaultItems(0)
                            ->columnSpanFull(),

                        TextInput::make('settings.team_heading')
                            ->label(__('Team heading'))
                            ->maxLength(255),

                        Textarea::make('settings.team_subheading')
                            ->label(__('Team subheading'))
                            ->rows(2)
                            ->maxLength(500),

                        Repeater::make('settings.team')
                            ->label(__('Team members'))
                            ->schema([
                                TextInput::make('name')
                                    ->label(__('app.name'))
                                    ->required()
                                    ->maxLength(100),
                                TextInput::make('role')
                                    ->label(__('Role'))
                                    ->maxLength(100),
                                Textarea::make('bio')
                                    ->label(__('Bio'))
                                    ->rows(2)
                                    ->maxLength(300),
                                FileUpload::make('photo')
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

                        Fieldset::make(__('Call to action'))
                            ->schema([
                                TextInput::make('settings.cta_heading')
                                    ->label(__('Heading'))
                                    ->maxLength(255),
                                TextInput::make('settings.cta_label')
                                    ->label(__('Button label'))
                                    ->maxLength(50),
                                Textarea::make('settings.cta_text')
                                    ->label(__('Text'))
                                    ->rows(2)
                                    ->maxLength(500)
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->columns(1)
                    ->collapsed(),

                Section::make(__('Contact Page Sections'))
                    ->visible(fn (Get $get) => $get('slug') === 'contact')
                    ->schema([
                        TextInput::make('settings.header_title')
                            ->label(__('Header title'))
                            ->maxLength(255),

                        Textarea::make('settings.header_subtitle')
                            ->label(__('Header subtitle'))
                            ->rows(2)
                            ->maxLength(500),

                        Textarea::make('settings.form_intro')
                            ->label(__('Form introduction'))
                            ->rows(2)
                            ->maxLength(500),

                        Repeater::make('settings.office_hours')
                            ->label(__('Office hours'))
                            ->schema([
                                TextInput::make('label')
                                    ->label(__('Days'))
                                    ->placeholder(__('Monday - Friday'))
                                    ->required()
                                    ->maxLength(100),
                                TextInput::make('value')
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

                Section::make(__('app.page_settings'))
                    ->schema([
                        Toggle::make('is_enabled')
                            ->label(__('app.page_enabled'))
                            ->helperText(__('app.page_enabled_helper'))
                            ->default(true),

                        Toggle::make('is_public')
                            ->label(__('app.page_public'))
                            ->helperText(__('app.page_public_helper'))
                            ->default(true),

                        TextInput::make('sort_order')
                            ->label(__('app.sort_order'))
                            ->numeric()
                            ->default(0)
                            ->helperText(__('app.sort_order_helper')),
                    ])
                    ->columns(3),
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label(__('app.title'))
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),

                TextColumn::make('slug')
                    ->label(__('app.slug'))
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->badge()
                    ->color('gray'),

                TextColumn::make('status')
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

                TextColumn::make('published_at')
                    ->label(__('app.published'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),

                IconColumn::make('is_enabled')
                    ->label(__('app.enabled'))
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->sortable(),

                IconColumn::make('is_public')
                    ->label(__('app.public'))
                    ->boolean()
                    ->trueIcon('heroicon-o-globe-alt')
                    ->falseIcon('heroicon-o-eye-slash')
                    ->trueColor('info')
                    ->falseColor('warning')
                    ->sortable(),

                TextColumn::make('sort_order')
                    ->label(__('app.order'))
                    ->numeric()
                    ->sortable()
                    ->alignCenter(),

                TextColumn::make('created_at')
                    ->label(__('app.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label(__('app.updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label(__('app.status'))
                    ->options([
                        'draft' => __('app.draft'),
                        'published' => __('app.published'),
                        'scheduled' => __('app.scheduled'),
                    ]),

                TernaryFilter::make('is_enabled')
                    ->label(__('app.enabled'))
                    ->placeholder(__('app.all'))
                    ->trueLabel(__('app.enabled_only'))
                    ->falseLabel(__('app.disabled_only')),

                TernaryFilter::make('is_public')
                    ->label(__('app.public'))
                    ->placeholder(__('app.all'))
                    ->trueLabel(__('app.public_only'))
                    ->falseLabel(__('app.private_only')),
            ])
            ->recordActions([
                Action::make('preview')
                    ->label(__('app.preview'))
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->url(fn (Page $record): string => route('page.show', ['slug' => $record->slug, 'preview' => true]))
                    ->openUrlInNewTab(),

                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
                
                Action::make('view_page')
                    ->label(__('app.view_live'))
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->color('success')
                    ->url(fn (Page $record): string => route('page.show', $record->slug))
                    ->openUrlInNewTab()
                    ->visible(fn (Page $record): bool => $record->isPublished() && $record->is_enabled && $record->is_public),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    
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
            'index' => ListPages::route('/'),
            'create' => CreatePage::route('/create'),
            'view' => ViewPage::route('/{record}'),
            'edit' => EditPage::route('/{record}/edit'),
        ];
    }
}