<?php

declare(strict_types=1);

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->in('Feature', 'Browser');

/**
 * The user factory always creates a personal team, so a missing one is a broken
 * fixture rather than something a test should assert around.
 */
function personalTeam(User $user): Team
{
    return $user->personalTeam() ?? throw new RuntimeException("User [{$user->id}] has no personal team.");
}
