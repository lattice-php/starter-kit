<?php
declare(strict_types=1);

namespace App\Pages\Concerns;

use App\Models\User;
use Lattice\Lattice\Core\Enums\Variant;
use Lattice\Lattice\Realtime\Listen;

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
                ->toast(__('You have a new team invitation.'), Variant::Info),
            Listen::private('App.Models.User.'.$user->id)
                ->on('.RemovedFromTeam')
                ->toast(__('You were removed from a team.'), Variant::Warning),
        ];
    }
}
