<?php
declare(strict_types=1);

use App\Actions\Teams\SwitchTeam;
use App\Enums\TeamRole;
use App\Forms\Teams\CreateTeamForm;
use App\Forms\Teams\DeleteTeamForm;
use App\Forms\Teams\UpdateTeamForm;
use App\Models\Team;
use App\Models\User;

test('the teams index page can be rendered', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('teams.index'))
        ->assertOk();
});

test('teams can be created', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->submitForm(CreateTeamForm::class, ['name' => 'Test Team'])
        ->assertRedirect();

    $this->assertDatabaseHas('teams', [
        'name' => 'Test Team',
        'is_personal' => false,
    ]);
});

test('team slug uses next available suffix', function () {
    $user = User::factory()->create();

    Team::factory()->create(['name' => 'Acme', 'slug' => 'acme']);
    Team::factory()->create(['name' => 'Acme One', 'slug' => 'acme-1']);
    Team::factory()->create(['name' => 'Acme Ten', 'slug' => 'acme-10']);

    $this->actingAs($user)
        ->submitForm(CreateTeamForm::class, ['name' => 'Acme']);

    $this->assertDatabaseHas('teams', [
        'name' => 'Acme',
        'slug' => 'acme-11',
    ]);
});

test('the team edit page can be rendered', function () {
    $user = User::factory()->create();
    $team = Team::factory()->create();

    $team->members()->attach($user, ['role' => TeamRole::Owner->value]);

    $this->actingAs($user)
        ->get(route('teams.edit', $team))
        ->assertOk();
});

test('teams can be updated by owners', function () {
    $user = User::factory()->create();
    $team = Team::factory()->create(['name' => 'Original Name']);

    $team->members()->attach($user, ['role' => TeamRole::Owner->value]);

    $this->actingAs($user)
        ->submitForm(UpdateTeamForm::class, ['name' => 'Updated Name'], ['team' => $team->slug])
        ->assertRedirect(route('teams.edit', $team->fresh()));

    $this->assertDatabaseHas('teams', [
        'id' => $team->id,
        'name' => 'Updated Name',
    ]);
});

test('teams cannot be updated by members', function () {
    $owner = User::factory()->create();
    $member = User::factory()->create();
    $team = Team::factory()->create();

    $team->members()->attach($owner, ['role' => TeamRole::Owner->value]);
    $team->members()->attach($member, ['role' => TeamRole::Member->value]);

    $this->actingAs($member)
        ->submitDeniedForm(UpdateTeamForm::class, ['name' => 'Updated Name'], ['team' => $team->slug])
        ->assertForbidden();
});

test('teams can be deleted by owners', function () {
    $user = User::factory()->create();
    $team = Team::factory()->create();

    $team->members()->attach($user, ['role' => TeamRole::Owner->value]);

    $this->actingAs($user)
        ->submitForm(DeleteTeamForm::class, ['name' => $team->name], ['team' => $team->slug])
        ->assertRedirect();

    $this->assertSoftDeleted('teams', ['id' => $team->id]);
});

test('team deletion requires name confirmation', function () {
    $user = User::factory()->create();
    $team = Team::factory()->create();

    $team->members()->attach($user, ['role' => TeamRole::Owner->value]);

    $this->actingAs($user)
        ->submitForm(DeleteTeamForm::class, ['name' => 'Wrong Name'], ['team' => $team->slug])
        ->assertSessionHasErrors('name');

    $this->assertDatabaseHas('teams', [
        'id' => $team->id,
        'deleted_at' => null,
    ]);
});

test('deleting current team switches to alphabetically first remaining team', function () {
    $user = User::factory()->create(['name' => 'Mike']);

    $zuluTeam = Team::factory()->create(['name' => 'Zulu Team']);
    $zuluTeam->members()->attach($user, ['role' => TeamRole::Owner->value]);

    $alphaTeam = Team::factory()->create(['name' => 'Alpha Team']);
    $alphaTeam->members()->attach($user, ['role' => TeamRole::Owner->value]);

    $betaTeam = Team::factory()->create(['name' => 'Beta Team']);
    $betaTeam->members()->attach($user, ['role' => TeamRole::Owner->value]);

    $user->update(['current_team_id' => $zuluTeam->id]);

    $this->actingAs($user)
        ->submitForm(DeleteTeamForm::class, ['name' => $zuluTeam->name], ['team' => $zuluTeam->slug])
        ->assertRedirect();

    $this->assertSoftDeleted('teams', ['id' => $zuluTeam->id]);

    expect($user->refresh()->current_team_id)->toEqual($alphaTeam->id);
});

test('deleting current team falls back to personal team when alphabetically first', function () {
    $user = User::factory()->create();
    $personalTeam = personalTeam($user);
    $team = Team::factory()->create(['name' => 'Zulu Team']);
    $team->members()->attach($user, ['role' => TeamRole::Owner->value]);

    $user->update(['current_team_id' => $team->id]);

    $this->actingAs($user)
        ->submitForm(DeleteTeamForm::class, ['name' => $team->name], ['team' => $team->slug])
        ->assertRedirect();

    $this->assertSoftDeleted('teams', ['id' => $team->id]);

    expect($user->refresh()->current_team_id)->toEqual($personalTeam->id);
});

test('deleting non current team leaves current team unchanged', function () {
    $user = User::factory()->create();
    $personalTeam = personalTeam($user);
    $team = Team::factory()->create();
    $team->members()->attach($user, ['role' => TeamRole::Owner->value]);

    $user->update(['current_team_id' => $personalTeam->id]);

    $this->actingAs($user)
        ->submitForm(DeleteTeamForm::class, ['name' => $team->name], ['team' => $team->slug])
        ->assertRedirect();

    $this->assertSoftDeleted('teams', ['id' => $team->id]);

    expect($user->refresh()->current_team_id)->toEqual($personalTeam->id);
});

test('deleting team switches other affected users to their personal team', function () {
    $owner = User::factory()->create();
    $member = User::factory()->create();

    $team = Team::factory()->create();
    $team->members()->attach($owner, ['role' => TeamRole::Owner->value]);
    $team->members()->attach($member, ['role' => TeamRole::Member->value]);

    $owner->update(['current_team_id' => $team->id]);
    $member->update(['current_team_id' => $team->id]);

    $this->actingAs($owner)
        ->submitForm(DeleteTeamForm::class, ['name' => $team->name], ['team' => $team->slug])
        ->assertRedirect();

    expect($member->refresh()->current_team_id)->toEqual(personalTeam($member)->id);
});

test('personal teams cannot be deleted', function () {
    $user = User::factory()->create();
    $personalTeam = personalTeam($user);

    $this->actingAs($user)
        ->submitDeniedForm(DeleteTeamForm::class, ['name' => $personalTeam->name], ['team' => $personalTeam->slug])
        ->assertForbidden();

    $this->assertDatabaseHas('teams', [
        'id' => $personalTeam->id,
        'deleted_at' => null,
    ]);
});

test('teams cannot be deleted by non owners', function () {
    $owner = User::factory()->create();
    $member = User::factory()->create();
    $team = Team::factory()->create();

    $team->members()->attach($owner, ['role' => TeamRole::Owner->value]);
    $team->members()->attach($member, ['role' => TeamRole::Member->value]);

    $this->actingAs($member)
        ->submitDeniedForm(DeleteTeamForm::class, ['name' => $team->name], ['team' => $team->slug])
        ->assertForbidden();
});

test('users can switch teams', function () {
    $user = User::factory()->create();
    $team = Team::factory()->create();

    $team->members()->attach($user, ['role' => TeamRole::Member->value]);

    $this->actingAs($user)
        ->callAction(SwitchTeam::class, [], ['team' => $team->slug])
        ->assertOk()
        ->assertJsonFragment(['type' => 'redirect', 'url' => route('dashboard', ['current_team' => $team->slug])]);

    expect($user->refresh()->current_team_id)->toEqual($team->id);
});

test('users cannot switch to team they dont belong to', function () {
    $user = User::factory()->create();
    $team = Team::factory()->create();

    $this->actingAs($user)
        ->callDeniedAction(SwitchTeam::class, [], ['team' => $team->slug])
        ->assertForbidden();
});

test('guests cannot access teams', function () {
    $this->get(route('teams.index'))
        ->assertRedirect(route('login'));
});
