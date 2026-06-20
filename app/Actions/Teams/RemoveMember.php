<?php
declare(strict_types=1);

namespace App\Actions\Teams;

use App\Concerns\ResolvesCurrentUser;
use App\Concerns\ResolvesTeamFromContext;
use App\Events\Teams\RemovedFromTeam;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;
use Lattice\Lattice\Actions\ActionDefinition;
use Lattice\Lattice\Actions\ActionResult;
use Lattice\Lattice\Actions\Components\Action;
use Lattice\Lattice\Attributes\AsAction;
use Lattice\Lattice\Core\Enums\ButtonVariant;
use Lattice\Lattice\Core\Enums\HttpMethod;
use Lattice\Lattice\Core\Enums\Variant;

#[AsAction('teams.members.remove')]
class RemoveMember extends ActionDefinition
{
    use ResolvesCurrentUser;
    use ResolvesTeamFromContext;

    public function definition(Action $action): Action
    {
        return $action
            ->label(__('Remove'))
            ->method(HttpMethod::Delete)
            ->variant(ButtonVariant::Destructive)
            ->confirm(
                title: __('Remove member?'),
                description: __('This user will lose access to the team.'),
                confirmLabel: __('Remove member'),
            );
    }

    #[\Override]
    public function authorize(Request $request): bool
    {
        return $this->currentUser()->can('removeMember', $this->teamFromContext());
    }

    public function handle(Request $request): ActionResult
    {
        $team = $this->teamFromContext();
        $member = User::findOrFail($this->contextInt('member'));

        abort_if($team->owner()?->is($member), 403, __('The team owner cannot be removed.'));

        $team->memberships()->where('user_id', $member->id)->delete();

        RemovedFromTeam::dispatch($member, $team);

        if ($member->isCurrentTeam($team)) {
            $personalTeam = $member->personalTeam();

            if ($personalTeam instanceof Team) {
                $member->switchTeam($personalTeam);
            }
        }

        return ActionResult::success()
            ->toast(Variant::Success, __('Member removed.'))
            ->reloadComponent('teams.members');
    }
}
