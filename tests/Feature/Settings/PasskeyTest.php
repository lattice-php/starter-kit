<?php
declare(strict_types=1);

use App\Actions\Settings\DeletePasskey;
use App\Models\User;
use Laravel\Passkeys\Passkey;

function createPasskey(User $user, string $name = 'My passkey'): Passkey
{
    return $user->passkeys()->create([
        'name' => $name,
        'credential_id' => 'cred-'.fake()->unique()->uuid(),
        'credential' => ['type' => 'public-key'],
    ]);
}

test('users can delete their own passkey through the lattice action', function () {
    $user = User::factory()->create();
    $passkey = createPasskey($user);

    $this->actingAs($user)
        ->callAction(DeletePasskey::class, context: ['passkey' => $passkey->id])
        ->assertOk()
        ->assertJsonFragment(['type' => 'reload-component', 'component' => 'settings.passkeys']);

    expect($user->passkeys()->whereKey($passkey->id)->exists())->toBeFalse();
});

test('users cannot delete another users passkey', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();
    $passkey = createPasskey($other);

    $this->actingAs($user)
        ->callActionForged(DeletePasskey::class, context: ['passkey' => $passkey->id])
        ->assertForbidden();

    expect($other->passkeys()->whereKey($passkey->id)->exists())->toBeTrue();
});
