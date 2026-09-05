<?php
declare(strict_types=1);

use App\Actions\Teams\RemoveMember;
use App\Actions\Teams\UpdateMemberRole;
use App\Enums\TeamRole;
use App\Models\Team;
use App\Models\User;

test('team member roles can be updated by owners', function () {
    $owner = User::factory()->create();
    $member = User::factory()->create();
    $team = Team::factory()->create();

    $team->members()->attach($owner, ['role' => TeamRole::Owner->value]);
    $team->members()->attach($member, ['role' => TeamRole::Member->value]);

    $this->actingAs($owner)
        ->callAction(UpdateMemberRole::class, ['role' => TeamRole::Admin->value], ['team' => $team->slug, 'member' => $member->id])
        ->assertOk();

    expect($member->teamRole($team))->toBe(TeamRole::Admin);
});

test('team member roles cannot be updated by non owners', function () {
    $owner = User::factory()->create();
    $admin = User::factory()->create();
    $member = User::factory()->create();
    $team = Team::factory()->create();

    $team->members()->attach($owner, ['role' => TeamRole::Owner->value]);
    $team->members()->attach($admin, ['role' => TeamRole::Admin->value]);
    $team->members()->attach($member, ['role' => TeamRole::Member->value]);

    $this->actingAs($admin)
        ->callDeniedAction(UpdateMemberRole::class, ['role' => TeamRole::Admin->value], ['team' => $team->slug, 'member' => $member->id])
        ->assertForbidden();
});

test('team members can be removed by owners', function () {
    $owner = User::factory()->create();
    $member = User::factory()->create();
    $team = Team::factory()->create();

    $team->members()->attach($owner, ['role' => TeamRole::Owner->value]);
    $team->members()->attach($member, ['role' => TeamRole::Member->value]);

    $this->actingAs($owner)
        ->callAction(RemoveMember::class, [], ['team' => $team->slug, 'member' => $member->id])
        ->assertOk();

    expect($member->refresh()->belongsToTeam($team))->toBeFalse();
});

test('team members cannot be removed by non owners', function () {
    $owner = User::factory()->create();
    $admin = User::factory()->create();
    $member = User::factory()->create();
    $team = Team::factory()->create();

    $team->members()->attach($owner, ['role' => TeamRole::Owner->value]);
    $team->members()->attach($admin, ['role' => TeamRole::Admin->value]);
    $team->members()->attach($member, ['role' => TeamRole::Member->value]);

    $this->actingAs($admin)
        ->callDeniedAction(RemoveMember::class, [], ['team' => $team->slug, 'member' => $member->id])
        ->assertForbidden();
});

test('team owner cannot be removed', function () {
    $owner = User::factory()->create();
    $team = Team::factory()->create();

    $team->members()->attach($owner, ['role' => TeamRole::Owner->value]);

    $this->actingAs($owner)
        ->callAction(RemoveMember::class, [], ['team' => $team->slug, 'member' => $owner->id])
        ->assertForbidden();

    expect($owner->refresh()->belongsToTeam($team))->toBeTrue();
});

test('team member role cannot be set to owner', function () {
    $owner = User::factory()->create();
    $member = User::factory()->create();
    $team = Team::factory()->create();

    $team->members()->attach($owner, ['role' => TeamRole::Owner->value]);
    $team->members()->attach($member, ['role' => TeamRole::Member->value]);

    $this->actingAs($owner)
        ->callAction(UpdateMemberRole::class, ['role' => TeamRole::Owner->value], ['team' => $team->slug, 'member' => $member->id])
        ->assertSessionHasErrors('role');

    expect($member->teamRole($team))->toBe(TeamRole::Member);
});

test('removed member current team is set to personal team', function () {
    $owner = User::factory()->create();
    $member = User::factory()->create();
    $personalTeam = personalTeam($member);
    $team = Team::factory()->create();

    $team->members()->attach($owner, ['role' => TeamRole::Owner->value]);
    $team->members()->attach($member, ['role' => TeamRole::Member->value]);

    $member->update(['current_team_id' => $team->id]);

    $this->actingAs($owner)
        ->callAction(RemoveMember::class, [], ['team' => $team->slug, 'member' => $member->id]);

    expect($member->refresh()->current_team_id)->toEqual($personalTeam->id);
});
