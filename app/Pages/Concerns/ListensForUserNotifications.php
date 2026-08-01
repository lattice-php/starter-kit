<?php
declare(strict_types=1);

namespace App\Pages\Concerns;

use App\Models\User;
use Lattice\Lattice\Realtime\Listen;
use Lattice\Lattice\Ui\Enums\Variant;

trait ListensForUserNotifications
{
    /**
     * @return array<int, Listen>
     */
    protected function listeners(): array
    {
        $user = request()->user();

        if (! $user instanceof User) {
            return [];
        }

        return [
            Listen::private('App.Models.User.'.$user->id)
                ->on('.TeamInvitationReceived')
                ->toast(__('teams.notification.received-toast'), Variant::Info),
            Listen::private('App.Models.User.'.$user->id)
                ->on('.RemovedFromTeam')
                ->toast(__('teams.notification.removed-toast'), Variant::Warning),
        ];
    }
}
