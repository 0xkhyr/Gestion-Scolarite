<?php

namespace App\Filament\Pages\Account;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

class Profile extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-user-circle';
    
    protected static string $view = 'filament.pages.account.profile';
    
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

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('app.profile_information'))
                    ->description(__('app.profile_information_desc'))
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label(__('app.full_name'))
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->label(__('app.email'))
                            ->email()
                            ->disabled(fn () => ! auth()->user()->hasRole('super_admin'))
                            ->dehydrated(fn () => auth()->user()->hasRole('super_admin'))
                            ->required(fn () => auth()->user()->hasRole('super_admin'))
                            ->unique(table: 'users', column: 'email', ignorable: auth()->user())
                            ->helperText(fn () => auth()->user()->hasRole('super_admin') ? null : __('app.email_cannot_be_changed')),
                        Forms\Components\TextInput::make('phone')
                            ->label(__('app.phone_number'))
                            ->tel()
                            ->maxLength(20),
                    ])->columns(2),

                Forms\Components\Section::make(__('app.account_details'))
                    ->description(__('app.account_details_desc'))
                    ->schema([
                        Forms\Components\Placeholder::make('role')
                            ->label(__('app.role'))
                            ->content(fn () => auth()->user()->roles->pluck('name')->map(fn ($r) => __('app.' . $r))->join(', ') ?: __('app.no_roles_assigned')),
                        Forms\Components\Placeholder::make('created_at')
                            ->label(__('app.account_created'))
                            ->content(fn () => auth()->user()->created_at?->translatedFormat('d M Y') ?: __('app.unknown')),
                        Forms\Components\Placeholder::make('last_login')
                            ->label(__('app.last_login'))
                            ->content(fn () => auth()->user()->last_login_at?->translatedFormat('d M Y H:i') ?: __('app.never')),
                        Forms\Components\Placeholder::make('profile_info')
                            ->label(__('app.profile_type'))
                            ->content(function () {
                                $user = auth()->user();
                                if ($user->profile) {
                                    return __('app.' . strtolower(class_basename($user->profile_type)));
                                }
                                return __('app.no_profile_linked');
                            }),
                        Forms\Components\Placeholder::make('is_active')
                            ->label(__('app.account_status'))
                            ->content(function () {
                                $user = auth()->user();
                                $isActive = $user->is_active;
                                
                                if ($isActive) {
                                    return new \Illuminate\Support\HtmlString(
                                        '<div class="flex items-center gap-2">' .
                                        '<svg class="w-5 h-5 text-success-600 dark:text-success-400" fill="currentColor" viewBox="0 0 20 20">' .
                                        '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>' .
                                        '</svg>' .
                                        '<span class="text-sm font-medium text-success-600 dark:text-success-400">' . __('app.active') . '</span>' .
                                        '</div>'
                                    );
                                } else {
                                    return new \Illuminate\Support\HtmlString(
                                        '<div class="flex items-center gap-2">' .
                                        '<svg class="w-5 h-5 text-danger-600 dark:text-danger-400" fill="currentColor" viewBox="0 0 20 20">' .
                                        '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>' .
                                        '</svg>' .
                                        '<span class="text-sm font-medium text-danger-600 dark:text-danger-400">' . __('app.inactive') . '</span>' .
                                        '</div>'
                                    );
                                }
                            }),
                    ])->columns(3),
                
                Forms\Components\Actions::make([
                    Forms\Components\Actions\Action::make('save')
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