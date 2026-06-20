<?php
declare(strict_types=1);

use App\Models\User;

test('new users can register', function () {
    $response = $this->post(route('register.store'), [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertAuthenticated();

    expect(User::where('email', 'test@example.com')->firstOrFail()->name)->toBe('Test User');
    $response->assertRedirect(route('dashboard.home'));
});
