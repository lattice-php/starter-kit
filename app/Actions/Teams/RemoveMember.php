<?php
declare(strict_types=1);

namespace App\Actions\Teams;

use App\Events\Teams\RemovedFromTeam;
use App\Models\Team;
use App\Models\User;
use Lattice\Actions\ActionDefinition;
use Lattice\Actions\ActionResult;
use Lattice\Actions\Components\Action;
use Lattice\Core\Attributes\AsAction;
use Lattice\Ui\Enums\HttpMethod;
use Lattice\Ui\Enums\Variant;

#[AsAction('teams.members.remove', can: 'removeMember', on: 'team')]
class RemoveMember extends ActionDefinition
{
    public function definition(Action $action): Action
    {
        return $action
            ->label(__('teams.members.remove'))
            ->method(HttpMethod::Delete)
            ->variant(Variant::Danger)
            ->confirm(
                title: __('teams.members.remove-confirm-title'),
                description: __('teams.members.remove-confirm-description'),
                confirmLabel: __('teams.members.remove-confirm-label'),
            );
    }

    public function handle(): ActionResult
    {
        /** @var Team $team */
        $team = $this->contextModel('team');
        /** @var User $member */
        $member = $this->contextModel('member');

        abort_if($team->owner()?->is($member) === true, 403, __('teams.members.owner-cannot-be-removed'));

        $team->memberships()->where('user_id', $member->id)->delete();

        RemovedFromTeam::dispatch($member, $team);

        if ($member->isCurrentTeam($team)) {
            $personalTeam = $member->personalTeam();

            if ($personalTeam instanceof Team) {
                $member->switchTeam($personalTeam);
            }
        }

        return ActionResult::success()
            ->toast(__('teams.members.removed'))
            ->reloadComponent('teams.members');
    }
}
