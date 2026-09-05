<?php
declare(strict_types=1);

namespace App\Actions\Settings;

use App\Concerns\ResolvesCurrentUser;
use Laravel\Fortify\Actions\GenerateNewRecoveryCodes;
use Laravel\Fortify\Features;
use Lattice\Actions\ActionDefinition;
use Lattice\Actions\ActionResult;
use Lattice\Actions\Components\Action as ActionComponent;
use Lattice\Core\Attributes\AsAction;
use Lattice\Ui\Enums\HttpMethod;
use Lattice\Ui\Enums\Variant;

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
            ->variant(Variant::Secondary)
            ->confirm(
                title: __('settings.recovery-codes.regenerate-confirm-title'),
                description: __('settings.recovery-codes.regenerate-confirm-description'),
                confirmLabel: __('settings.recovery-codes.regenerate'),
            );
    }

    public function handle(): ActionResult
    {
        $user = $this->currentUser();

        abort_unless(Features::canManageTwoFactorAuthentication(), 403);

        ($this->generate)($user);

        return ActionResult::success()
            ->toast(__('settings.recovery-codes.regenerated'))
            ->reloadPage();
    }
}
