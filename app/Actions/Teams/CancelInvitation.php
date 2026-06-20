<?php
declare(strict_types=1);

namespace App\Actions\Teams;

use App\Concerns\ResolvesCurrentUser;
use App\Concerns\ResolvesTeamFromContext;
use Illuminate\Http\Request;
use Lattice\Lattice\Actions\ActionDefinition;
use Lattice\Lattice\Actions\ActionResult;
use Lattice\Lattice\Actions\Components\Action;
use Lattice\Lattice\Attributes\AsAction;
use Lattice\Lattice\Core\Enums\ButtonVariant;
use Lattice\Lattice\Core\Enums\HttpMethod;
use Lattice\Lattice\Core\Enums\Variant;

#[AsAction('teams.invitations.cancel')]
class CancelInvitation extends ActionDefinition
{
    use ResolvesCurrentUser;
    use ResolvesTeamFromContext;

    public function definition(Action $action): Action
    {
        return $action
            ->label(__('Cancel'))
            ->method(HttpMethod::Delete)
            ->variant(ButtonVariant::Destructive)
            ->confirm(
                title: __('Cancel invitation?'),
                description: __('The invitation link will stop working.'),
                confirmLabel: __('Cancel invitation'),
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
            ->toast(Variant::Success, __('Invitation cancelled.'))
            ->reloadComponent('teams.invitations');
    }
}
