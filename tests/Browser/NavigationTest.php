<?php
declare(strict_types=1);

use App\Models\User;

it('opens the navigation drawer on a narrow viewport', function () {
    $user = User::factory()->create(['name' => 'Ada Lovelace']);

    $this->actingAs($user);

    // The sidebar ships no trigger of its own, so the topbar button is the only
    // way into the navigation below the md breakpoint.
    visit('/dashboard')->on()->mobile()
        ->assertMissing('[data-test="sidebar-backdrop"]')
        ->click('[data-test="sidebar-toggle"]')
        ->assertVisible('[data-test="sidebar-backdrop"]')
        ->assertSee("Ada Lovelace's Team")
        ->assertNoJavaScriptErrors();
});
