<?php
declare(strict_types=1);

namespace App\Pages;

use App\Actions\Settings\DisableTwoFactorAuthenticationAction;
use App\Actions\Settings\EnableTwoFactorAuthenticationAction;
use App\Actions\Settings\RegenerateRecoveryCodesAction;
use App\Components\PageHeader;
use App\Components\Settings\PasskeyRegistration;
use App\Concerns\ResolvesCurrentUser;
use App\Forms\Settings\DeleteAccountForm;
use App\Forms\Settings\PasswordSettingsForm;
use App\Forms\Settings\ProfileSettingsForm;
use App\Http\Requests\Settings\TwoFactorAuthenticationRequest;
use App\Tables\Settings\PasskeysTable;
use Illuminate\Http\Request;
use Laravel\Fortify\Features;
use Lattice\Actions\Components\Action;
use Lattice\Core\Attributes\AsPage;
use Lattice\Form\Components\Form;
use Lattice\Table\Components\Table;
use Lattice\Ui\Components\Component;
use Lattice\Ui\Components\Heading;
use Lattice\Ui\Components\SegmentedControl;
use Lattice\Ui\Components\Stack;
use Lattice\Ui\Components\Tab;
use Lattice\Ui\Components\Tabs;
use Lattice\Ui\Components\Text;
use Lattice\Ui\Enums\Gap;
use Lattice\Ui\Enums\Width;
use Lattice\Ui\PageSchema;

// No `verified` here, unlike every other app page: the profile tab is where an
// unverified user resends their verification mail.
#[AsPage(route: 'settings', name: 'settings.edit', middleware: ['auth'])]
class SettingsPage extends AppPage
{
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
                    PageHeader::make('settings-heading', __('settings.heading'), __('settings.subtitle')),
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
            PageHeader::section('profile-heading', __('settings.profile.heading'), __('settings.profile.subtitle')),
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
            PageHeader::section('security-heading', __('settings.security.heading'), __('settings.security.subtitle')),
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
            $this->recoveryCodesSection($recoveryCodes)
                ->visible($canManageTwoFactor && $twoFactorEnabled),
            PageHeader::section('passkey-heading', __('settings.passkeys.heading'), __('settings.passkeys.subtitle'))
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
            PageHeader::section('appearance-heading', __('settings.appearance.heading'), __('settings.appearance.subtitle')),
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
