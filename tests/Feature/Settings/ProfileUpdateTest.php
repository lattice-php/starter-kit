<?php

declare(strict_types=1);

use App\Forms\Settings\ProfileSettingsForm;
use App\Models\User;

test('users can update their name and email', function () {
    $user = User::factory()->create([
        'name' => 'Old Name',
        'email' => 'old@example.com',
    ]);

    $this->actingAs($user)
        ->submitForm(ProfileSettingsForm::class, [
            'name' => 'New Name',
            'email' => 'new@example.com',
        ])
        ->assertRedirect(route('settings.edit'));

    $user->refresh();

    expect($user->name)->toBe('New Name')
        ->and($user->email)->toBe('new@example.com');
});

test('changing the email resets the verification status', function () {
    $user = User::factory()->create([
        'email' => 'old@example.com',
        'email_verified_at' => now(),
    ]);

    $this->actingAs($user)
        ->submitForm(ProfileSettingsForm::class, [
            'name' => $user->name,
            'email' => 'new@example.com',
        ])
        ->assertRedirect();

    expect($user->fresh()->email_verified_at)->toBeNull();
});

test('updating with an unchanged email keeps the verification status', function () {
    $user = User::factory()->create([
        'email' => 'same@example.com',
        'email_verified_at' => now(),
    ]);

    $this->actingAs($user)
        ->submitForm(ProfileSettingsForm::class, [
            'name' => 'Renamed',
            'email' => 'same@example.com',
        ])
        ->assertRedirect();

    expect($user->fresh()->email_verified_at)->not->toBeNull();
});
