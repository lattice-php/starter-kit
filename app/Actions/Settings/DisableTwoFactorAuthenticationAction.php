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
use Lattice\Lattice\Core\Enums\ButtonVariant;
use Lattice\Lattice\Core\Enums\HttpMethod;
use Lattice\Lattice\Core\Enums\Variant;

#[AsAction('settings.two-factor.disable')]
class DisableTwoFactorAuthenticationAction extends ActionDefinition
{
    use ResolvesCurrentUser;

    public function __construct(private readonly DisableTwoFactorAuthentication $disable) {}

    public function definition(ActionComponent $action): ActionComponent
    {
        return $action
            ->label('Disable 2FA')
            ->method(HttpMethod::Post)
            ->variant(ButtonVariant::Destructive)
            ->confirm(
                title: 'Disable two-factor authentication?',
                description: 'Your account will no longer require a one-time code during sign in.',
                confirmLabel: 'Disable 2FA',
            );
    }

    public function handle(Request $request): ActionResult
    {
        $user = $this->currentUser();

        abort_unless(Features::canManageTwoFactorAuthentication(), 403);

        ($this->disable)($user);

        return ActionResult::success()
            ->toast(Variant::Success, __('Two-factor authentication disabled.'))
            ->reloadPage();
    }
}
