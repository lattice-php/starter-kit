<?php
declare(strict_types=1);

namespace App\Actions\Settings;

use App\Concerns\ResolvesCurrentUser;
use Illuminate\Http\Request;
use Laravel\Fortify\Actions\GenerateNewRecoveryCodes;
use Laravel\Fortify\Features;
use Lattice\Lattice\Actions\ActionDefinition;
use Lattice\Lattice\Actions\ActionResult;
use Lattice\Lattice\Actions\Components\Action as ActionComponent;
use Lattice\Lattice\Attributes\AsAction;
use Lattice\Lattice\Ui\Enums\ButtonVariant;
use Lattice\Lattice\Core\Enums\HttpMethod;
use Lattice\Lattice\Ui\Enums\Variant;

#[AsAction('settings.two-factor.regenerate-recovery-codes')]
class RegenerateRecoveryCodesAction extends ActionDefinition
{
    use ResolvesCurrentUser;

    public function __construct(private readonly GenerateNewRecoveryCodes $generate) {}

    public function definition(ActionComponent $action): ActionComponent
    {
        return $action
            ->label(__('settings.recovery-codes.regenerate'))
            ->method(HttpMethod::Post)
            ->variant(ButtonVariant::Secondary)
            ->confirm(
                title: __('settings.recovery-codes.regenerate-confirm-title'),
                description: __('settings.recovery-codes.regenerate-confirm-description'),
                confirmLabel: __('settings.recovery-codes.regenerate'),
            );
    }

    public function handle(Request $request): ActionResult
    {
        $user = $this->currentUser();

        abort_unless(Features::canManageTwoFactorAuthentication(), 403);

        ($this->generate)($user);

        return ActionResult::success()
            ->toast(Variant::Success, __('settings.recovery-codes.regenerated'))
            ->reloadPage();
    }
}
