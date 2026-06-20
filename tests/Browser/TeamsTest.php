<?php
declare(strict_types=1);

use App\Enums\TeamRole;
use App\Models\Team;
use App\Models\TeamInvitation;
use App\Models\User;

it('renders teams and opens team editing from the row action', function () {
    $user = User::factory()->create([
        'name' => 'Ada Lovelace',
        'email' => 'ada@example.com',
    ]);
    $team = Team::factory()->create([
        'name' => 'Lattice Core',
        'slug' => 'lattice-core',
    ]);
    $member = User::factory()->create([
        'name' => 'Grace Hopper',
        'email' => 'grace@example.com',
    ]);

    $team->members()->attach($user, ['role' => TeamRole::Owner->value]);
    $team->members()->attach($member, ['role' => TeamRole::Member->value]);

    $invitation = TeamInvitation::factory()->create([
        'team_id' => $team->id,
        'email' => 'invited@example.com',
        'role' => TeamRole::Member,
        'invited_by' => $user->id,
    ]);

    $this->actingAs($user);

    $page = visit('/settings/teams');

    $page->assertSee('Teams')
        ->assertSee('Lattice Core')
        ->assertNoJavaScriptErrors()
        ->click('[data-lattice-component="teams.'.$team->id.'.edit"]')
        ->assertPathIs('/settings/teams/lattice-core')
        ->assertSee('Manage team settings, members, and invitations.')
        ->assertSee('Grace Hopper')
        ->click('[data-lattice-component="teams.members.'.$member->id.'.actions"] button')
        ->assertSee('Change role')
        ->assertSee('Remove')
        ->click('[data-lattice-component="teams.invitations.'.$invitation->id.'.actions"] button')
        ->assertSee('Cancel')
        ->assertNoJavaScriptErrors();
});
