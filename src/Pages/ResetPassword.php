<?php

namespace EightyNine\FilamentPasswordExpiry\Pages;

use EightyNine\FilamentPasswordExpiry\Events\NewPasswordSet;
use EightyNine\FilamentPasswordExpiry\Http\Response\PasswordResetResponse;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Form;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Pages\SimplePage;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput; 
use Filament\Notifications\Notification; 
use Illuminate\Contracts\Support\Htmlable; 
use Illuminate\Support\Facades\Hash; 
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rules\Password as PasswordRule; 

class ResetPassword extends SimplePage
{

    use InteractsWithFormActions;

    /**
     * @var view-string
     */
    protected static string $view = 'password-expiry::pages.reset-password';

    public ?string $current_password = '';

    public ?string $password = '';

    public ?string $passwordConfirmation = '';


    public function form(Form $form): Form
    {
        return $form
            ->schema([
                $this->getCurrentPasswordFormComponent(),
                $this->getPasswordFormComponent(),
                $this->getPasswordConfirmationFormComponent(),
            ]);
    }

    protected function getCurrentPasswordFormComponent(): Component
    {
        return TextInput::make('current_password')
            ->label(__('password-expiry::password-expiry.reset-password.form.current_password.label'))
            ->password()
            ->revealable(filament()->arePasswordsRevealable())
            ->required()
            ->rule(PasswordRule::default())
            ->validationAttribute(__('password-expiry::password-expiry.reset-password.form.current_password.validation_attribute'));
    }

    protected function getPasswordFormComponent(): Component
    {
        return TextInput::make('password')
            ->label(__('password-expiry::password-expiry.reset-password.form.password.label'))
            ->password()
            ->revealable(filament()->arePasswordsRevealable())
            ->required()
            ->rule(PasswordRule::default())
            ->same('passwordConfirmation')
            ->validationAttribute(__('password-expiry::password-expiry.reset-password.form.password.validation_attribute'));
    }

    protected function getPasswordConfirmationFormComponent(): Component
    {
        return TextInput::make('passwordConfirmation')
            ->label(__('password-expiry::password-expiry.reset-password.form.password_confirmation.label'))
            ->password()
            ->revealable(filament()->arePasswordsRevealable())
            ->required()
            ->dehydrated(false);
    }

    public function getTitle(): string | Htmlable
    {
        return __('password-expiry::password-expiry.reset-password.title');
    }

    public function getHeading(): string | Htmlable
    {
        return __('password-expiry::password-expiry.reset-password.heading');
    }

    public function getSubHeading(): string | Htmlable
    {
        return __('password-expiry::password-expiry.reset-password.sub_heading');
    }

    /**
     * @return array<Action | ActionGroup>
     */
    protected function getFormActions(): array
    {
        return [
            $this->getResetPasswordFormAction(),
        ];
    }

    public function getResetPasswordFormAction(): Action
    {
        return Action::make('resetPassword')
            ->label(__('password-expiry::password-expiry.reset-password.reset_password'))
            ->submit('resetPassword');
    }

    public function hasLogo(): bool
    {
        return false;
    }

    public function resetPassword(): ?PasswordResetResponse
    {
        // get new password
        $data = $this->form->getState();

        // get auth object
        $authObject = config('password-expiry.auth_class')::auth()->user();

        // check if current password is correct
        $current_password = $this->form->getState()['current_password'];
        if (!Hash::check($current_password, $authObject->{config('password-expiry.password_column_name')})) {
            Notification::make()
                ->title(__('password-expiry::password-expiry.reset-password.notifications.wrong_password.title'))
                ->body(__('password-expiry::password-expiry.reset-password.notifications.wrong_password.body'))
                ->danger()
                ->send();
            return null;
        }

        // check if both required columns exist in the database
        if (!Schema::hasColumn(config('password-expiry.table_name'), config('password-expiry.column_name'))) {
            Notification::make()
                ->title(__('password-expiry::password-expiry.reset-password.notifications.column_not_found.title'))
                ->body(__('password-expiry::password-expiry.reset-password.notifications.column_not_found.body', [
                    'column_name' => config('password-expiry.column_name'),
                    'password_column_name' => config('password-expiry.password_column_name'),
                    'table_name' => config('password-expiry.table_name'),
                ]))
                ->danger()
                ->send();
            return null;
        }


        // get password expiry date and time
        $passwordExpiryDateTime = now()->addDays(config('password-expiry.expires_in'));

        // set password expiry date and time
        $authObject->{config('password-expiry.column_name')} = $passwordExpiryDateTime;
        $authObject->{config('password-expiry.password_column_name')} = $data['password'];
        $authObject->save();

        // load up user email
        $data[config('password-expiry.email_column_name')] = $authObject->{config('password-expiry.email_column_name')};

        event(new NewPasswordSet($authObject));

        Notification::make()
            ->title(__('password-expiry::password-expiry.reset-password.notifications.password_reset.success'))
            ->success()
            ->send();

        return new PasswordResetResponse();
    }
}
