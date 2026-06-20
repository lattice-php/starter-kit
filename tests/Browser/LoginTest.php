<?php
declare(strict_types=1);

use App\Models\User;

it('logs in a user with valid credentials', function () {
    $user = User::factory()->create([
        'email' => 'ada@example.com',
        'password' => 'password',
    ]);
    $team = $user->currentTeam()->firstOrFail();

    $page = visit('/login');

    $page->assertSee('Log in to your account')
        ->assertNoJavaScriptErrors()
        ->fill('email', 'ada@example.com')
        ->fill('password', 'password')
        ->click('button[type="submit"]')
        ->assertPathIs('/'.$team->slug.'/dashboard');

    $this->assertAuthenticatedAs($user);
});
