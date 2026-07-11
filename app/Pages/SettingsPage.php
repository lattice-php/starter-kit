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
use Lattice\Lattice\Ui\Components\Component;
use Lattice\Lattice\Ui\Components\Heading;
use Lattice\Lattice\Ui\Components\Modal;
use Lattice\Lattice\Ui\Components\SegmentedControl;
use Lattice\Lattice\Ui\Components\Stack;
use Lattice\Lattice\Ui\Components\Tab;
use Lattice\Lattice\Ui\Components\Tabs;
use Lattice\Lattice\Ui\Components\Text;
use Lattice\Lattice\Ui\Enums\Gap;
use Lattice\Lattice\Ui\Enums\PageContainer;
use Lattice\Lattice\Ui\Enums\PageLayout;
use Lattice\Lattice\Ui\Enums\Width;
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
        return __('settings.title');
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
                            Heading::make(__('settings.heading'), 1),
                            Text::make(__('settings.subtitle')),
                        ]),
                    Tabs::make('settings-tabs')
                        ->defaultValue('profile')
                        ->schema([
                            Tab::make('profile', __('settings.tabs.profile'))->schema($this->profileTab()),
                            Tab::make('security', __('settings.tabs.security'))
                                ->confirm(route('password.confirm', absolute: false))
                                ->schema($this->securityTab(
                                    canManageTwoFactor: $canManageTwoFactor,
                                    twoFactorEnabled: $user->hasEnabledTwoFactorAuthentication(),
                                    canManagePasskeys: Features::canManagePasskeys(),
                                    recoveryCodes: $user->hasEnabledTwoFactorAuthentication()
                                        ? $user->recoveryCodes()
                                        : [],
                                )),
                            Tab::make('appearance', __('settings.tabs.appearance'))->schema($this->appearanceTab(
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
                    Heading::make(__('settings.profile.heading'), 2),
                    Text::make(__('settings.profile.subtitle')),
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
                    Heading::make(__('settings.security.heading'), 2),
                    Text::make(__('settings.security.subtitle')),
                ]),
            Form::use(PasswordSettingsForm::class),
            Stack::make('two-factor-authentication')
                ->gap(Gap::Small)
                ->schema([
                    Heading::make(__('settings.two-factor.heading'), 2),
                    Text::make($this->twoFactorDescription($twoFactorEnabled)),
                    Action::use(EnableTwoFactorAuthenticationAction::class)
                        ->visible(! $twoFactorEnabled),
                    Action::use(DisableTwoFactorAuthenticationAction::class)
                        ->visible($twoFactorEnabled),
                ])
                ->visible($canManageTwoFactor),
            Modal::make('settings.two-factor-setup')
                ->title(__('settings.two-factor.setup-title'))
                ->description(__('settings.two-factor.setup-description'))
                ->schema([
                    Fragment::lazy(TwoFactorSetupFragment::class),
                ])
                ->visible($canManageTwoFactor),
            $this->recoveryCodesSection($recoveryCodes)
                ->visible($canManageTwoFactor && $twoFactorEnabled),
            Stack::make('passkey-heading')
                ->gap(Gap::Small)
                ->schema([
                    Heading::make(__('settings.passkeys.heading'), 2),
                    Text::make(__('settings.passkeys.subtitle')),
                ])
                ->visible($canManagePasskeys),
            Table::lazy(PasskeysTable::class)
                ->visible($canManagePasskeys),
            PasskeyRegistration::make()
                ->visible($canManagePasskeys),
        ];
    }

    private function twoFactorDescription(bool $twoFactorEnabled): string
    {
        if ($twoFactorEnabled) {
            return __('settings.two-factor.description-enabled');
        }

        return __('settings.two-factor.description-disabled');
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
                Heading::make(__('settings.recovery-codes.heading'), 2),
                Text::make(__('settings.recovery-codes.description')),
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
                    Heading::make(__('settings.appearance.heading'), 2),
                    Text::make(__('settings.appearance.subtitle')),
                ]),
            SegmentedControl::make('appearance', __('settings.appearance.label'))
                ->value($appearance)
                ->emits('lattice:appearance-change')
                ->options([
                    SegmentedControl::option(__('settings.appearance.light'), 'light'),
                    SegmentedControl::option(__('settings.appearance.dark'), 'dark'),
                    SegmentedControl::option(__('settings.appearance.system'), 'system'),
                ]),
        ];
    }

    private function currentAppearance(Request $request): string
    {
        $appearance = $request->cookie('appearance', 'system');

        return in_array($appearance, ['light', 'dark', 'system'], true) ? $appearance : 'system';
    }
}
