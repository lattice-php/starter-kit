<?php
declare(strict_types=1);

use App\Actions\Settings\DisableTwoFactorAuthenticationAction;
use App\Actions\Settings\EnableTwoFactorAuthenticationAction;
use App\Forms\Settings\ConfirmTwoFactorForm;
use App\Models\User;
use Laravel\Fortify\Actions\EnableTwoFactorAuthentication;
use Laravel\Fortify\Features;
use Laravel\Fortify\Fortify;
use PragmaRX\Google2FA\Google2FA;

test('security page requires password confirmation when enabled', function () {
    $this->skipUnlessFortifyHas(Features::twoFactorAuthentication());

    $user = User::factory()->create();

    Features::twoFactorAuthentication([
        'confirm' => true,
        'confirmPassword' => true,
    ]);

    $response = $this->actingAs($user)
        ->get(route('settings.edit', ['tabs' => 'security']));

    $response->assertRedirect(route('password.confirm'));
});

test('confirming a valid code through the lattice form enables two factor', function () {
    $this->skipUnlessFortifyHas(Features::twoFactorAuthentication());

    $user = User::factory()->create();
    app(EnableTwoFactorAuthentication::class)($user);
    $user->refresh();

    $secret = Fortify::currentEncrypter()->decrypt((string) $user->two_factor_secret);
    $code = app(Google2FA::class)->getCurrentOtp($secret);

    $this->actingAs($user)
        ->withSession(['auth.password_confirmed_at' => time()])
        ->submitForm(ConfirmTwoFactorForm::class, ['code' => $code])
        ->assertRedirect();

    expect($user->refresh()->hasEnabledTwoFactorAuthentication())->toBeTrue();
});

test('the security tab lists recovery codes when two factor is enabled', function () {
    $this->skipUnlessFortifyHas(Features::twoFactorAuthentication());

    $user = User::factory()->withTwoFactor()->create();

    $response = $this->actingAs($user)
        ->withSession(['auth.password_confirmed_at' => time()])
        ->get(route('settings.edit', ['tabs' => 'security']));

    $response->assertOk()
        ->assertSee('Recovery codes')
        ->assertSee('recovery-code-1');
});

test('enabling two factor ships the setup modal', function () {
    $this->skipUnlessFortifyHas(Features::twoFactorAuthentication());

    $user = User::factory()->create();

    $this->actingAs($user)
        ->withSession(['auth.password_confirmed_at' => time()])
        ->callAction(EnableTwoFactorAuthenticationAction::class)
        ->assertOk()
        ->assertJsonFragment(['type' => 'open-modal']);

    expect($user->refresh()->two_factor_secret)->not->toBeNull();
});

test('two factor lattice actions require the fortify feature', function () {
    config(['fortify.features' => []]);

    $user = User::factory()->create();
    $this->actingAs($user);

    $this->callAction(EnableTwoFactorAuthenticationAction::class)->assertForbidden();
    $this->callAction(DisableTwoFactorAuthenticationAction::class)->assertForbidden();
});
