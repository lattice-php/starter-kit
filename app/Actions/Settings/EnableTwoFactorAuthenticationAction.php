<?php
declare(strict_types=1);

namespace App\Actions\Settings;

use App\Concerns\ResolvesCurrentUser;
use App\Fragments\Settings\TwoFactorSetupFragment;
use Illuminate\Http\Request;
use Laravel\Fortify\Actions\EnableTwoFactorAuthentication;
use Laravel\Fortify\Features;
use Lattice\Actions\ActionDefinition;
use Lattice\Actions\ActionResult;
use Lattice\Actions\Components\Action as ActionComponent;
use Lattice\Core\Attributes\AsAction;
use Lattice\Fragments\Components\Fragment;
use Lattice\Ui\Components\Modal;
use Lattice\Ui\Enums\HttpMethod;
use Lattice\Ui\Enums\Variant;

#[AsAction('settings.two-factor.enable')]
class EnableTwoFactorAuthenticationAction extends ActionDefinition
{
    use ResolvesCurrentUser;

    public function __construct(private readonly EnableTwoFactorAuthentication $enable) {}

    public function definition(ActionComponent $action): ActionComponent
    {
        return $action
            ->label(__('settings.two-factor.enable'))
            ->method(HttpMethod::Post);
    }

    public function handle(Request $request): ActionResult
    {
        $user = $this->currentUser();

        abort_unless(Features::canManageTwoFactorAuthentication(), 403);

        ($this->enable)($user);

        return ActionResult::success()
            ->toast(__('settings.two-factor.setup-started'), Variant::Info)
            ->openModal(
                Modal::make('settings.two-factor-setup')
                    ->title(__('settings.two-factor.setup-title'))
                    ->description(__('settings.two-factor.setup-description'))
                    ->schema([
                        Fragment::lazy(TwoFactorSetupFragment::class),
                    ]),
            );
    }
}
