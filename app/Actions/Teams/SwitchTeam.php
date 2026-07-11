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
use Lattice\Lattice\Ui\Enums\Variant;

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
            ->toast(Variant::Success, __('teams.switch.switched', ['team' => $team->name]))
            ->toRoute('dashboard', ['current_team' => $team->slug]);
    }
}
