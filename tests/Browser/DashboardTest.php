<?php
declare(strict_types=1);

use App\Models\User;

it('renders the dashboard for the current team', function () {
    $user = User::factory()->create([
        'name' => 'John Doe',
        'email' => 'john@example.com',
    ]);
    $team = $user->currentTeam()->firstOrFail();

    $this->actingAs($user);

    $page = visit('/'.$team->slug.'/dashboard');

    $page->assertSee('Dashboard')
        ->assertSee('John Doe')
        ->assertNoJavaScriptErrors();
});
