<?php
declare(strict_types=1);

namespace App\Actions\Teams;

use App\Concerns\ResolvesCurrentUser;
use App\Models\Team;
use Lattice\Actions\ActionDefinition;
use Lattice\Actions\ActionResult;
use Lattice\Actions\Components\Action;
use Lattice\Core\Attributes\AsAction;

#[AsAction('teams.switch', can: 'view', on: 'team')]
class SwitchTeam extends ActionDefinition
{
    use ResolvesCurrentUser;

    public function definition(Action $action): Action
    {
        return $action->label(__('teams.switch.label'));
    }

    public function handle(): ActionResult
    {
        /** @var Team $team */
        $team = $this->contextModel('team');

        $this->currentUser()->switchTeam($team);

        return ActionResult::success()
            ->toast(__('teams.switch.switched', ['team' => $team->name]))
            ->toRoute('dashboard', ['current_team' => $team->slug]);
    }
}
