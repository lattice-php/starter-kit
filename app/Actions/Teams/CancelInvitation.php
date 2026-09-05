<?php
declare(strict_types=1);

namespace App\Actions\Teams;

use App\Models\TeamInvitation;
use Lattice\Actions\ActionDefinition;
use Lattice\Actions\ActionResult;
use Lattice\Actions\Components\Action;
use Lattice\Core\Attributes\AsAction;
use Lattice\Ui\Enums\HttpMethod;
use Lattice\Ui\Enums\Variant;

#[AsAction('teams.invitations.cancel', can: 'cancelInvitation', on: 'team')]
class CancelInvitation extends ActionDefinition
{
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

    public function handle(): ActionResult
    {
        /** @var TeamInvitation $invitation */
        $invitation = $this->contextModel('invitation');

        $invitation->delete();

        return ActionResult::success()
            ->toast(__('teams.invitations.cancelled'))
            ->reloadComponent('teams.invitations');
    }
}
