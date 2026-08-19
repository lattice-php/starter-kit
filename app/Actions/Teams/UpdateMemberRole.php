<?php
declare(strict_types=1);

namespace App\Actions\Teams;

use App\Concerns\ResolvesCurrentUser;
use App\Concerns\ResolvesTeamFromContext;
use App\Enums\TeamRole;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Lattice\Actions\ActionDefinition;
use Lattice\Actions\ActionResult;
use Lattice\Actions\Components\Action;
use Lattice\Core\Attributes\AsAction;
use Lattice\Form\Components\Choice;
use Lattice\Ui\Enums\HttpMethod;
use Lattice\Ui\Enums\Variant;

#[AsAction('teams.members.update')]
class UpdateMemberRole extends ActionDefinition
{
    use ResolvesCurrentUser;
    use ResolvesTeamFromContext;

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

    #[\Override]
    public function authorize(Request $request): bool
    {
        return $this->currentUser()->can('updateMember', $this->teamFromContext());
    }

    public function handle(Request $request): ActionResult
    {
        $team = $this->teamFromContext();
        $validated = $this->validate($request);
        $member = User::findOrFail($this->contextInt('member'));

        $team->memberships()
            ->where('user_id', $member->id)
            ->firstOrFail()
            ->update(['role' => TeamRole::from((string) $validated['role'])]);

        return ActionResult::success()
            ->toast(__('teams.members.role-updated'))
            ->reloadComponent('teams.members');
    }
}
