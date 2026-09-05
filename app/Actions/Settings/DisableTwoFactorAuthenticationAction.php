<?php
declare(strict_types=1);

namespace App\Actions\Settings;

use App\Concerns\ResolvesCurrentUser;
use Laravel\Fortify\Actions\DisableTwoFactorAuthentication;
use Laravel\Fortify\Features;
use Lattice\Actions\ActionDefinition;
use Lattice\Actions\ActionResult;
use Lattice\Actions\Components\Action as ActionComponent;
use Lattice\Core\Attributes\AsAction;
use Lattice\Ui\Enums\HttpMethod;
use Lattice\Ui\Enums\Variant;

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

    public function handle(): ActionResult
    {
        $user = $this->currentUser();

        abort_unless(Features::canManageTwoFactorAuthentication(), 403);

        ($this->disable)($user);

        return ActionResult::success()
            ->toast(__('settings.two-factor.disabled-toast'))
            ->reloadPage();
    }
}
