<?php
declare(strict_types=1);

namespace App\Concerns;

use App\Models\User;

trait ResolvesCurrentUser
{
    protected function currentUser(): User
    {
        $user = auth()->user();

        abort_unless($user instanceof User, 403);

        return $user;
    }
}
