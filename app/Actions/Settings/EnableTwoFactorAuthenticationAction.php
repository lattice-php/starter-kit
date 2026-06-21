<?php
declare(strict_types=1);

namespace App\Actions\Settings;

use App\Concerns\ResolvesCurrentUser;
use Illuminate\Http\Request;
use Laravel\Fortify\Actions\EnableTwoFactorAuthentication;
use Laravel\Fortify\Features;
use Lattice\Lattice\Actions\ActionDefinition;
use Lattice\Lattice\Actions\ActionResult;
use Lattice\Lattice\Actions\Components\Action as ActionComponent;
use Lattice\Lattice\Attributes\AsAction;
use Lattice\Lattice\Core\Enums\HttpMethod;
use Lattice\Lattice\Core\Enums\Variant;

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
            ->toast(Variant::Info, __('settings.two-factor.setup-started'))
            ->openModal('settings.two-factor-setup');
    }
}
