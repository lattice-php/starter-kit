<?php
declare(strict_types=1);

use App\Enums\TeamPermission;
use App\Enums\TeamRole;

test('the owner has every permission', function () {
    foreach (TeamPermission::cases() as $permission) {
        expect(TeamRole::Owner->hasPermission($permission))->toBeTrue();
    }
});

test('an admin can manage the team and invitations but not members or deletion', function () {
    expect(TeamRole::Admin->hasPermission(TeamPermission::UpdateTeam))->toBeTrue()
        ->and(TeamRole::Admin->hasPermission(TeamPermission::CreateInvitation))->toBeTrue()
        ->and(TeamRole::Admin->hasPermission(TeamPermission::CancelInvitation))->toBeTrue()
        ->and(TeamRole::Admin->hasPermission(TeamPermission::DeleteTeam))->toBeFalse()
        ->and(TeamRole::Admin->hasPermission(TeamPermission::UpdateMember))->toBeFalse()
        ->and(TeamRole::Admin->hasPermission(TeamPermission::RemoveMember))->toBeFalse();
});

test('a member has no permissions', function () {
    foreach (TeamPermission::cases() as $permission) {
        expect(TeamRole::Member->hasPermission($permission))->toBeFalse();
    }
});

test('the owner role cannot be assigned to members', function () {
    expect(TeamRole::assignableCases())->toBe([TeamRole::Admin, TeamRole::Member]);
});
