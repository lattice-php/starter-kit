<?php
declare(strict_types=1);

namespace App\Pages;

use App\Actions\Settings\DisableTwoFactorAuthenticationAction;
use App\Actions\Settings\EnableTwoFactorAuthenticationAction;
use App\Actions\Settings\RegenerateRecoveryCodesAction;
use App\Components\Settings\PasskeyRegistration;
use App\Concerns\ResolvesCurrentUser;
use App\Forms\Settings\DeleteAccountForm;
use App\Forms\Settings\PasswordSettingsForm;
use App\Forms\Settings\ProfileSettingsForm;
use App\Fragments\Settings\TwoFactorSetupFragment;
use App\Http\Requests\Settings\TwoFactorAuthenticationRequest;
use App\Pages\Concerns\ListensForUserNotifications;
use App\Tables\Settings\PasskeysTable;
use Illuminate\Http\Request;
use Laravel\Fortify\Features;
use Lattice\Lattice\Actions\Components\Action;
use Lattice\Lattice\Attributes\AsPage;
use Lattice\Lattice\Core\Components\Component;
use Lattice\Lattice\Core\Components\Heading;
use Lattice\Lattice\Core\Components\Modal;
use Lattice\Lattice\Core\Components\SegmentedControl;
use Lattice\Lattice\Core\Components\Stack;
use Lattice\Lattice\Core\Components\Tab;
use Lattice\Lattice\Core\Components\Tabs;
use Lattice\Lattice\Core\Components\Text;
use Lattice\Lattice\Core\Enums\Gap;
use Lattice\Lattice\Core\Enums\PageContainer;
use Lattice\Lattice\Core\Enums\PageLayout;
use Lattice\Lattice\Core\Enums\Width;
use Lattice\Lattice\Core\PageSchema;
use Lattice\Lattice\Forms\Components\Form;
use Lattice\Lattice\Fragments\Components\Fragment;
use Lattice\Lattice\Http\Page;
use Lattice\Lattice\Tables\Components\Table;

#[AsPage(route: 'settings', name: 'settings.edit', layout: PageLayout::App, container: PageContainer::Default, middleware: ['web', 'auth'])]
class SettingsPage extends Page
{
    use ListensForUserNotifications;
    use ResolvesCurrentUser;

    public function title(): string
    {
        return 'Settings';
    }

    public function render(PageSchema $schema, TwoFactorAuthenticationRequest $request): PageSchema
    {
        $user = $this->currentUser();

        $canManageTwoFactor = Features::canManageTwoFactorAuthentication();

        return $schema->schema([
            Stack::make('settings-page')
                ->gap(Gap::Large)
                ->width(Width::Medium)
                ->schema([
                    Stack::make('settings-heading')
                        ->gap(Gap::Small)
                        ->schema([
                            Heading::make('Settings', 1),
                            Text::make('Manage your profile, security, and appearance settings.'),
                        ]),
                    Tabs::make('settings-tabs')
                        ->defaultValue('profile')
                        ->schema([
                            Tab::make('profile', 'Profile')->schema($this->profileTab()),
                            Tab::make('security', 'Security')
                                ->confirm(route('password.confirm', absolute: false))
                                ->schema($this->securityTab(
                                    canManageTwoFactor: $canManageTwoFactor,
                                    twoFactorEnabled: $user->hasEnabledTwoFactorAuthentication(),
                                    canManagePasskeys: Features::canManagePasskeys(),
                                    recoveryCodes: $user->hasEnabledTwoFactorAuthentication()
                                        ? $user->recoveryCodes()
                                        : [],
                                )),
                            Tab::make('appearance', 'Appearance')->schema($this->appearanceTab(
                                $this->currentAppearance($request),
                            )),
                        ]),
                ]),
        ]);
    }

    /**
     * @return array<int, Component>
     */
    private function profileTab(): array
    {
        return [
            Stack::make('profile-heading')
                ->gap(Gap::Small)
                ->schema([
                    Heading::make('Profile', 2),
                    Text::make('Update your name and email address.'),
                ]),
            Form::use(ProfileSettingsForm::class),
            Form::use(DeleteAccountForm::class),
        ];
    }

    /**
     * @param  array<int, string>  $recoveryCodes
     * @return array<int, Component>
     */
    private function securityTab(
        bool $canManageTwoFactor,
        bool $twoFactorEnabled,
        bool $canManagePasskeys,
        array $recoveryCodes = [],
    ): array {
        return [
            Stack::make('security-heading')
                ->gap(Gap::Small)
                ->schema([
                    Heading::make('Security', 2),
                    Text::make('Update your password and manage sign-in security.'),
                ]),
            Form::use(PasswordSettingsForm::class),
            Stack::make('two-factor-authentication')
                ->gap(Gap::Small)
                ->schema([
                    Heading::make('Two-factor authentication', 2),
                    Text::make($this->twoFactorDescription($twoFactorEnabled)),
                    Action::use(EnableTwoFactorAuthenticationAction::class)
                        ->when(! $twoFactorEnabled),
                    Action::use(DisableTwoFactorAuthenticationAction::class)
                        ->when($twoFactorEnabled),
                ])
                ->when($canManageTwoFactor),
            Modal::make('settings.two-factor-setup')
                ->title('Set up two-factor authentication')
                ->description('Scan the QR code with your authenticator app.')
                ->schema([
                    Fragment::lazy(TwoFactorSetupFragment::class),
                ])
                ->when($canManageTwoFactor),
            $this->recoveryCodesSection($recoveryCodes)
                ->when($canManageTwoFactor && $twoFactorEnabled),
            Stack::make('passkey-heading')
                ->gap(Gap::Small)
                ->schema([
                    Heading::make('Passkeys', 2),
                    Text::make('Manage your passkeys for passwordless sign-in.'),
                ])
                ->when($canManagePasskeys),
            Table::lazy(PasskeysTable::class)
                ->when($canManagePasskeys),
            PasskeyRegistration::make()
                ->when($canManagePasskeys),
        ];
    }

    private function twoFactorDescription(bool $twoFactorEnabled): string
    {
        if ($twoFactorEnabled) {
            return 'You will be prompted for a secure one-time code during sign in.';
        }

        return 'Add an authenticator app code requirement to protect your account during sign in.';
    }

    /**
     * @param  array<int, string>  $recoveryCodes
     */
    private function recoveryCodesSection(array $recoveryCodes): Stack
    {
        $codeNodes = [];

        foreach ($recoveryCodes as $index => $code) {
            $codeNodes[] = Text::make($code, 'recovery-code-'.$index);
        }

        return Stack::make('two-factor-recovery-codes')
            ->gap(Gap::Small)
            ->schema([
                Heading::make('Recovery codes', 2),
                Text::make('Store these codes in a safe place. Each one can be used once to access your account if you lose your authenticator.'),
                Stack::make('recovery-codes-list')
                    ->gap(Gap::Small)
                    ->schema($codeNodes),
                Action::use(RegenerateRecoveryCodesAction::class),
            ]);
    }

    /**
     * @return array<int, Component>
     */
    private function appearanceTab(string $appearance): array
    {
        return [
            Stack::make('appearance-heading')
                ->gap(Gap::Small)
                ->schema([
                    Heading::make('Appearance settings', 2),
                    Text::make('Update the appearance settings for your account.'),
                ]),
            SegmentedControl::make('appearance', 'Appearance')
                ->value($appearance)
                ->emits('lattice:appearance-change')
                ->options([
                    SegmentedControl::option('Light', 'light'),
                    SegmentedControl::option('Dark', 'dark'),
                    SegmentedControl::option('System', 'system'),
                ]),
        ];
    }

    private function currentAppearance(Request $request): string
    {
        $appearance = $request->cookie('appearance', 'system');

        return in_array($appearance, ['light', 'dark', 'system'], true) ? $appearance : 'system';
    }
}
