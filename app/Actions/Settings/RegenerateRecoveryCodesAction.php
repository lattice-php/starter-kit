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
use Lattice\Lattice\Core\Enums\ButtonVariant;
use Lattice\Lattice\Core\Enums\HttpMethod;
use Lattice\Lattice\Core\Enums\Variant;

#[AsAction('settings.two-factor.regenerate-recovery-codes')]
class RegenerateRecoveryCodesAction extends ActionDefinition
{
    use ResolvesCurrentUser;

    public function __construct(private readonly GenerateNewRecoveryCodes $generate) {}

    public function definition(ActionComponent $action): ActionComponent
    {
        return $action
            ->label('Regenerate codes')
            ->method(HttpMethod::Post)
            ->variant(ButtonVariant::Secondary)
            ->confirm(
                title: 'Regenerate recovery codes?',
                description: 'Your existing recovery codes will stop working and be replaced with a new set.',
                confirmLabel: 'Regenerate codes',
            );
    }

    public function handle(Request $request): ActionResult
    {
        $user = $this->currentUser();

        abort_unless(Features::canManageTwoFactorAuthentication(), 403);

        ($this->generate)($user);

        return ActionResult::success()
            ->toast(Variant::Success, __('Recovery codes regenerated.'))
            ->reloadPage();
    }
}
