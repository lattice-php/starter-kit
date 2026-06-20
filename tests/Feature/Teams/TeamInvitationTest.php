<?php
declare(strict_types=1);

use App\Actions\Teams\CancelInvitation;
use App\Enums\TeamRole;
use App\Events\Teams\TeamInvitationReceived;
use App\Forms\Teams\InviteTeamMemberForm;
use App\Models\Team;
use App\Models\TeamInvitation;
use App\Models\User;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;

test('team invitations can be created', function () {
    Notification::fake();

    $owner = User::factory()->create();
    $team = Team::factory()->create();

    $team->members()->attach($owner, ['role' => TeamRole::Owner->value]);

    $this->actingAs($owner)
        ->submitForm(InviteTeamMemberForm::class, ['email' => 'invited@example.com', 'role' => TeamRole::Member->value], ['team' => $team->slug])
        ->assertRedirect(route('teams.edit', $team));

    $this->assertDatabaseHas('team_invitations', [
        'team_id' => $team->id,
        'email' => 'invited@example.com',
        'role' => TeamRole::Member->value,
    ]);
});

test('team invitations can be created by admins', function () {
    Notification::fake();

    $owner = User::factory()->create();
    $admin = User::factory()->create();
    $team = Team::factory()->create();

    $team->members()->attach($owner, ['role' => TeamRole::Owner->value]);
    $team->members()->attach($admin, ['role' => TeamRole::Admin->value]);

    $this->actingAs($admin)
        ->submitForm(InviteTeamMemberForm::class, ['email' => 'invited@example.com', 'role' => TeamRole::Member->value], ['team' => $team->slug])
        ->assertRedirect(route('teams.edit', $team));
});

test('existing team members cannot be invited', function () {
    Notification::fake();

    $owner = User::factory()->create();
    $member = User::factory()->create(['email' => 'member@example.com']);
    $team = Team::factory()->create();

    $team->members()->attach($owner, ['role' => TeamRole::Owner->value]);
    $team->members()->attach($member, ['role' => TeamRole::Member->value]);

    $this->actingAs($owner)
        ->submitForm(InviteTeamMemberForm::class, ['email' => 'member@example.com', 'role' => TeamRole::Member->value], ['team' => $team->slug])
        ->assertSessionHasErrors('email');
});

test('duplicate invitations cannot be created', function () {
    Notification::fake();

    $owner = User::factory()->create();
    $team = Team::factory()->create();
    $team->members()->attach($owner, ['role' => TeamRole::Owner->value]);

    TeamInvitation::factory()->create([
        'team_id' => $team->id,
        'email' => 'invited@example.com',
        'invited_by' => $owner->id,
    ]);

    $this->actingAs($owner)
        ->submitForm(InviteTeamMemberForm::class, ['email' => 'invited@example.com', 'role' => TeamRole::Member->value], ['team' => $team->slug])
        ->assertSessionHasErrors('email');
});

test('team invitations cannot be created by members', function () {
    $owner = User::factory()->create();
    $member = User::factory()->create();
    $team = Team::factory()->create();

    $team->members()->attach($owner, ['role' => TeamRole::Owner->value]);
    $team->members()->attach($member, ['role' => TeamRole::Member->value]);

    $this->actingAs($member)
        ->submitForm(InviteTeamMemberForm::class, ['email' => 'invited@example.com', 'role' => TeamRole::Member->value], ['team' => $team->slug])
        ->assertForbidden();
});

test('team invitations can be cancelled by owners', function () {
    $owner = User::factory()->create();
    $team = Team::factory()->create();

    $team->members()->attach($owner, ['role' => TeamRole::Owner->value]);

    $invitation = TeamInvitation::factory()->create([
        'team_id' => $team->id,
        'invited_by' => $owner->id,
    ]);

    $this->actingAs($owner)
        ->callAction(CancelInvitation::class, [], ['team' => $team->slug, 'invitation' => $invitation->code])
        ->assertOk();

    $this->assertDatabaseMissing('team_invitations', ['id' => $invitation->id]);
});

test('team invitations can be accepted', function () {
    $owner = User::factory()->create();
    $invitedUser = User::factory()->create(['email' => 'invited@example.com']);
    $team = Team::factory()->create();

    $team->members()->attach($owner, ['role' => TeamRole::Owner->value]);

    $invitation = TeamInvitation::factory()->create([
        'team_id' => $team->id,
        'email' => 'invited@example.com',
        'role' => TeamRole::Member,
        'invited_by' => $owner->id,
    ]);

    $this->actingAs($invitedUser)
        ->get(route('invitations.accept', $invitation))
        ->assertRedirect(route('dashboard', ['current_team' => $team->slug]));

    expect($invitedUser->fresh()->belongsToTeam($team))->toBeTrue();
    expect($invitation->fresh()->accepted_at)->not->toBeNull();
});

test('team invitations cannot be accepted by uninvited user', function () {
    $owner = User::factory()->create();
    $uninvitedUser = User::factory()->create(['email' => 'uninvited@example.com']);
    $team = Team::factory()->create();

    $team->members()->attach($owner, ['role' => TeamRole::Owner->value]);

    $invitation = TeamInvitation::factory()->create([
        'team_id' => $team->id,
        'email' => 'invited@example.com',
        'invited_by' => $owner->id,
    ]);

    $this->actingAs($uninvitedUser)
        ->get(route('invitations.accept', $invitation))
        ->assertSessionHasErrors('invitation');

    expect($uninvitedUser->fresh()->belongsToTeam($team))->toBeFalse();
});

test('expired invitations cannot be accepted', function () {
    $owner = User::factory()->create();
    $invitedUser = User::factory()->create(['email' => 'invited@example.com']);
    $team = Team::factory()->create();

    $team->members()->attach($owner, ['role' => TeamRole::Owner->value]);

    $invitation = TeamInvitation::factory()->expired()->create([
        'team_id' => $team->id,
        'email' => 'invited@example.com',
        'invited_by' => $owner->id,
    ]);

    $this->actingAs($invitedUser)
        ->get(route('invitations.accept', $invitation))
        ->assertSessionHasErrors('invitation');

    expect($invitedUser->fresh()->belongsToTeam($team))->toBeFalse();
});

test('inviting an existing user broadcasts a realtime notification to them', function () {
    Notification::fake();
    Event::fake([TeamInvitationReceived::class]);

    $owner = User::factory()->create();
    $team = Team::factory()->create();
    $team->members()->attach($owner, ['role' => TeamRole::Owner->value]);

    $invited = User::factory()->create(['email' => 'invited@example.com']);

    $this->actingAs($owner)
        ->submitForm(InviteTeamMemberForm::class, ['email' => 'invited@example.com', 'role' => TeamRole::Member->value], ['team' => $team->slug])
        ->assertRedirect(route('teams.edit', $team));

    Event::assertDispatched(
        TeamInvitationReceived::class,
        fn (TeamInvitationReceived $event) => $event->recipient->is($invited) && $event->team->is($team),
    );
});

test('inviting an email without an account broadcasts nothing', function () {
    Notification::fake();
    Event::fake([TeamInvitationReceived::class]);

    $owner = User::factory()->create();
    $team = Team::factory()->create();
    $team->members()->attach($owner, ['role' => TeamRole::Owner->value]);

    $this->actingAs($owner)
        ->submitForm(InviteTeamMemberForm::class, ['email' => 'stranger@example.com', 'role' => TeamRole::Member->value], ['team' => $team->slug])
        ->assertRedirect(route('teams.edit', $team));

    Event::assertNotDispatched(TeamInvitationReceived::class);
});
