<?php
declare(strict_types=1);

namespace App\Enums;

use Lattice\Lattice\Core\Contracts\HasLabel;

enum TeamRole: string implements HasLabel
{
    case Owner = 'owner';
    case Admin = 'admin';
    case Member = 'member';

    public function getLabel(): string
    {
        return __('teams.roles.'.$this->value);
    }

    /**
     * @return array<TeamPermission>
     */
    public function permissions(): array
    {
        return match ($this) {
            self::Owner => TeamPermission::cases(),
            self::Admin => [
                TeamPermission::UpdateTeam,
                TeamPermission::CreateInvitation,
                TeamPermission::CancelInvitation,
            ],
            self::Member => [],
        };
    }

    public function hasPermission(TeamPermission $permission): bool
    {
        return in_array($permission, $this->permissions());
    }

    /**
     * @return array<int, self>
     */
    public static function assignableCases(): array
    {
        return array_values(array_filter(self::cases(), fn (self $role) => $role !== self::Owner));
    }
}
