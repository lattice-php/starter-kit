<?php
declare(strict_types=1);

use App\Models\User;
use Laravel\Fortify\Features;

beforeEach(function () {
    $this->skipUnlessFortifyHas(Features::twoFactorAuthentication());
});

it('renders the server-driven challenge form and logs in via a recovery code', function () {
    $user = User::factory()->withTwoFactor()->create([
        'email' => 'grace@example.com',
        'password' => 'password',
    ]);
    $team = $user->currentTeam()->firstOrFail();

    $page = visit('/login');

    $page->fill('email', 'grace@example.com')
        ->fill('password', 'password')
        ->click('button[type="submit"]')
        ->assertPathIs('/two-factor-challenge')
        ->assertNoJavaScriptErrors()
        ->assertSee('Authentication code')
        ->assertMissing('input[name="recovery_code"]')
        ->click('#use_recovery_code')
        ->assertVisible('input[name="recovery_code"]')
        ->fill('input[name="recovery_code"]', 'recovery-code-1')
        ->click('button[type="submit"]')
        ->assertPathIs('/'.$team->slug.'/dashboard');

    $this->assertAuthenticatedAs($user);
});
