<?php
declare(strict_types=1);

namespace App\Events\Teams;

use App\Models\Team;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TeamInvitationReceived implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public User $recipient,
        public Team $team,
    ) {}

    /**
     * @return array<int, PrivateChannel>
     */
    public function broadcastOn(): array
    {
        return [new PrivateChannel('App.Models.User.'.$this->recipient->id)];
    }

    public function broadcastAs(): string
    {
        return 'TeamInvitationReceived';
    }

    /**
     * @return array<string, string>
     */
    public function broadcastWith(): array
    {
        return ['team' => $this->team->name];
    }
}
