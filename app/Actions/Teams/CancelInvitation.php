<?php
declare(strict_types=1);

namespace App\Actions\Teams;

use App\Concerns\ResolvesCurrentUser;
use App\Concerns\ResolvesTeamFromContext;
use Illuminate\Http\Request;
use Lattice\Actions\ActionDefinition;
use Lattice\Actions\ActionResult;
use Lattice\Actions\Components\Action;
use Lattice\Core\Attributes\AsAction;
use Lattice\Ui\Enums\HttpMethod;
use Lattice\Ui\Enums\Variant;

#[AsAction('teams.invitations.cancel')]
class CancelInvitation extends ActionDefinition
{
    use ResolvesCurrentUser;
    use ResolvesTeamFromContext;

    public function definition(Action $action): Action
    {
        return $action
            ->label(__('teams.invitations.cancel'))
            ->method(HttpMethod::Delete)
            ->variant(Variant::Danger)
            ->confirm(
                title: __('teams.invitations.cancel-confirm-title'),
                description: __('teams.invitations.cancel-confirm-description'),
                confirmLabel: __('teams.invitations.cancel-confirm-label'),
            );
    }

    #[\Override]
    public function authorize(Request $request): bool
    {
        return $this->currentUser()->can('cancelInvitation', $this->teamFromContext());
    }

    public function handle(Request $request): ActionResult
    {
        $team = $this->teamFromContext();

        $team->invitations()
            ->where('code', $this->contextString('invitation'))
            ->firstOrFail()
            ->delete();

        return ActionResult::success()
            ->toast(__('teams.invitations.cancelled'))
            ->reloadComponent('teams.invitations');
    }
}
