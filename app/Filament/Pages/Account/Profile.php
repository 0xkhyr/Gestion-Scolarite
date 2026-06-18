<?php

namespace App\Filament\Pages\Account;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Placeholder;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\Blade;
use Filament\Schemas\Components\Actions;
use Filament\Forms;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

class Profile extends Page
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-user-circle';
    
    protected string $view = 'filament.pages.account.profile';
    
    protected static ?string $slug = 'account/profile';

    public function getTitle(): string
    {
        return __('app.profile');
    }

    public static function getNavigationLabel(): string
    {
        return __('app.my_account');
    }
    
    protected static bool $shouldRegisterNavigation = true;
    
    protected static ?int $navigationSort = 9999;

    public static function canViewAny(): bool
    {
        return auth()->user()->hasRole('super_admin') || auth()->user()->hasPermissionTo('setting.view');
    }

    public ?array $data = [];

    public function mount(): void
    {
        $user = auth()->user();
        $this->form->fill([
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->telephone,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('app.profile_information'))
                    ->description(__('app.profile_information_desc'))
                    ->icon('heroicon-o-user')
                    ->schema([
                        TextInput::make('name')
                            ->label(__('app.full_name'))
                            ->required()
                            ->maxLength(255),
                        TextInput::make('email')
                            ->label(__('app.email'))
                            ->email()
                            ->disabled(fn () => ! auth()->user()->hasRole('super_admin'))
                            ->dehydrated(fn () => auth()->user()->hasRole('super_admin'))
                            ->required(fn () => auth()->user()->hasRole('super_admin'))
                            ->unique(table: 'users', column: 'email', ignorable: auth()->user())
                            ->helperText(fn () => auth()->user()->hasRole('super_admin') ? null : __('app.email_cannot_be_changed')),
                        TextInput::make('phone')
                            ->label(__('app.phone_number'))
                            ->tel()
                            ->maxLength(20),
                    ])->columns(2),

                Section::make(__('app.account_details'))
                    ->description(__('app.account_details_desc'))
                    ->icon('heroicon-o-identification')
                    ->schema([
                        Placeholder::make('role')
                            ->label(__('app.role'))
                            ->content(function () {
                                $roles = auth()->user()->roles->pluck('name');

                                if ($roles->isEmpty()) {
                                    return new HtmlString(Blade::render(
                                        '<x-filament::badge color="gray">{{ $l }}</x-filament::badge>',
                                        ['l' => __('app.no_roles_assigned')],
                                    ));
                                }

                                $badges = $roles->map(fn ($r) => Blade::render(
                                    '<x-filament::badge :color="$c">{{ $l }}</x-filament::badge>',
                                    ['c' => $r === 'super_admin' ? 'primary' : 'info', 'l' => __('app.' . $r)],
                                ))->join('');

                                return new HtmlString('<div class="flex flex-wrap gap-1">' . $badges . '</div>');
                            }),
                        Placeholder::make('created_at')
                            ->label(__('app.account_created'))
                            ->content(fn () => auth()->user()->created_at?->translatedFormat('d M Y') ?: __('app.unknown')),
                        Placeholder::make('last_login')
                            ->label(__('app.last_login'))
                            ->content(fn () => auth()->user()->last_login_at?->translatedFormat('d M Y H:i') ?: __('app.never')),
                        Placeholder::make('profile_info')
                            ->label(__('app.profile_type'))
                            ->content(function () {
                                $user = auth()->user();

                                [$color, $label] = $user->profile
                                    ? ['info', __('app.' . strtolower(class_basename($user->profile_type)))]
                                    : ['gray', __('app.no_profile_linked')];

                                return new HtmlString(Blade::render(
                                    '<div class="flex"><x-filament::badge :color="$color">{{ $label }}</x-filament::badge></div>',
                                    compact('color', 'label'),
                                ));
                            }),
                        Placeholder::make('is_active')
                            ->label(__('app.account_status'))
                            ->content(function () {
                                $isActive = auth()->user()->is_active;

                                return new HtmlString(
                                    Blade::render(
                                        '<div class="flex"><x-filament::badge :color="$color">{{ $label }}</x-filament::badge></div>',
                                        $isActive
                                            ? ['color' => 'success', 'label' => __('app.active')]
                                            : ['color' => 'danger', 'label' => __('app.inactive')],
                                    )
                                );
                            }),
                    ])->columns(3),
                
                Actions::make([
                    Action::make('save')
                        ->label(__('app.save_changes'))
                        ->icon('heroicon-m-check-circle')
                        ->color('primary')
                        ->action(function () {
                            $this->save();
                        }),
                ])
            ])
            ->statePath('data');
    }

    protected function getFormActions(): array
    {
        return [
            // Actions are now handled within the form schema
        ];
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $user = auth()->user();

        $payload = [
            'name' => $data['name'],
            'telephone' => $data['phone'] ?? null,
        ];

        // Email is only editable by super admins
        if ($user->hasRole('super_admin') && ! empty($data['email'])) {
            $payload['email'] = $data['email'];
        }

        $user->update($payload);

        Notification::make()
            ->title(__('app.profile_updated'))
            ->success()
            ->send();
    }
}