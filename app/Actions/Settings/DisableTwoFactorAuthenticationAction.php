<?php
declare(strict_types=1);

namespace App\Actions\Settings;

use App\Concerns\ResolvesCurrentUser;
use Illuminate\Http\Request;
use Laravel\Fortify\Actions\DisableTwoFactorAuthentication;
use Laravel\Fortify\Features;
use Lattice\Lattice\Actions\ActionDefinition;
use Lattice\Lattice\Actions\ActionResult;
use Lattice\Lattice\Actions\Components\Action as ActionComponent;
use Lattice\Lattice\Attributes\AsAction;
use Lattice\Lattice\Ui\Enums\HttpMethod;
use Lattice\Lattice\Ui\Enums\Variant;

#[AsAction('settings.two-factor.disable')]
class DisableTwoFactorAuthenticationAction extends ActionDefinition
{
    use ResolvesCurrentUser;

    public function __construct(private readonly DisableTwoFactorAuthentication $disable) {}

    public function definition(ActionComponent $action): ActionComponent
    {
        return $action
            ->label(__('settings.two-factor.disable'))
            ->method(HttpMethod::Post)
            ->variant(Variant::Danger)
            ->confirm(
                title: __('settings.two-factor.disable-confirm-title'),
                description: __('settings.two-factor.disable-confirm-description'),
                confirmLabel: __('settings.two-factor.disable'),
            );
    }

    public function handle(Request $request): ActionResult
    {
        $user = $this->currentUser();

        abort_unless(Features::canManageTwoFactorAuthentication(), 403);

        ($this->disable)($user);

        return ActionResult::success()
            ->toast(__('settings.two-factor.disabled-toast'))
            ->reloadPage();
    }
}
