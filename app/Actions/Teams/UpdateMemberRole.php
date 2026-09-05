<?php
declare(strict_types=1);

namespace App\Actions\Teams;

use App\Enums\TeamRole;
use App\Models\Team;
use App\Models\User;
use Illuminate\Validation\Rule;
use Lattice\Actions\ActionDefinition;
use Lattice\Actions\ActionResult;
use Lattice\Actions\Components\Action;
use Lattice\Core\Attributes\AsAction;
use Lattice\Form\Components\Choice;
use Lattice\Form\FormData;
use Lattice\Ui\Enums\HttpMethod;
use Lattice\Ui\Enums\Variant;

#[AsAction('teams.members.update', can: 'updateMember', on: 'team')]
class UpdateMemberRole extends ActionDefinition
{
    public function definition(Action $action): Action
    {
        return $action
            ->label(__('teams.members.change-role'))
            ->method(HttpMethod::Patch)
            ->variant(Variant::Secondary)
            ->form([
                Choice::make('role', __('common.field.role'))
                    ->enum(TeamRole::assignableCases())
                    ->rules([Rule::enum(TeamRole::class)->only(TeamRole::assignableCases())])
                    ->required(),
            ]);
    }

    public function handle(FormData $data): ActionResult
    {
        /** @var Team $team */
        $team = $this->contextModel('team');
        /** @var User $member */
        $member = $this->contextModel('member');

        $team->memberships()
            ->where('user_id', $member->id)
            ->firstOrFail()
            ->update(['role' => $data->enum('role', TeamRole::class)]);

        return ActionResult::success()
            ->toast(__('teams.members.role-updated'))
            ->reloadComponent('teams.members');
    }
}
