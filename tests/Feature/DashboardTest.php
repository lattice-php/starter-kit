<?php
declare(strict_types=1);

use App\Models\User;

test('guests are redirected to the login page', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;

    $response = $this->get(route('dashboard.home', ['current_team' => $team]));
    $response->assertRedirect(route('login'));
});
