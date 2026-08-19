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

#[AsAction('teams.switch')]
class SwitchTeam extends ActionDefinition
{
    use ResolvesCurrentUser;
    use ResolvesTeamFromContext;

    public function definition(Action $action): Action
    {
        return $action->label(__('teams.switch.label'));
    }

    #[\Override]
    public function authorize(Request $request): bool
    {
        return $this->currentUser()->can('view', $this->teamFromContext());
    }

    public function handle(Request $request): ActionResult
    {
        $team = $this->teamFromContext();

        $this->currentUser()->switchTeam($team);

        return ActionResult::success()
            ->toast(__('teams.switch.switched', ['team' => $team->name]))
            ->toRoute('dashboard', ['current_team' => $team->slug]);
    }
}
