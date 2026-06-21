<?php
declare(strict_types=1);

return [
    'index' => [
        'title' => 'Teams',
        'heading' => 'Teams',
        'subtitle' => 'Manage your teams and team memberships.',
    ],

    'show' => [
        'subtitle' => 'Manage team settings, members, and invitations.',
        'details-heading' => 'Team details',
        'details-subtitle' => 'Update your team name.',
        'invite-heading' => 'Invite a member',
        'invite-subtitle' => 'Send an invitation to add someone to this team.',
        'members-heading' => 'Team members',
        'members-subtitle' => 'Manage who belongs to this team.',
        'invitations-heading' => 'Pending invitations',
        'invitations-subtitle' => "Invitations that haven't been accepted yet.",
    ],

    'fields' => [
        'name' => 'Team name',
    ],

    'columns' => [
        'team' => 'Team',
    ],

    'status' => [
        'personal' => 'Personal',
        'current' => 'Current',
    ],

    'actions' => [
        'edit' => 'Edit',
    ],

    'roles' => [
        'owner' => 'Owner',
        'admin' => 'Admin',
        'member' => 'Member',
    ],

    'create' => [
        'submit' => 'Create team',
        'created' => 'Team created.',
        'name-placeholder' => 'My team',
    ],

    'update' => [
        'submit' => 'Save team',
        'updated' => 'Team updated.',
    ],

    'delete' => [
        'warning' => 'Please proceed with caution, this cannot be undone.',
        'confirm-name' => 'Confirm team name',
        'name-mismatch' => 'The team name does not match.',
        'submit' => 'Delete team',
        'deleted' => 'Team deleted.',
    ],

    'invite' => [
        'submit' => 'Send invitation',
        'sent' => 'Invitation sent.',
        'email-placeholder' => 'colleague@example.com',
        'already-member' => 'This email is already a member or has a pending invitation.',
    ],

    'switch' => [
        'label' => 'Switch team',
        'switched' => 'Switched to :team.',
    ],

    'members' => [
        'column' => 'Member',
        'actions-label' => 'Member actions',
        'remove' => 'Remove',
        'remove-confirm-title' => 'Remove member?',
        'remove-confirm-description' => 'This user will lose access to the team.',
        'remove-confirm-label' => 'Remove member',
        'owner-cannot-be-removed' => 'The team owner cannot be removed.',
        'removed' => 'Member removed.',
        'change-role' => 'Change role',
        'role-updated' => 'Member role updated.',
    ],

    'invitations' => [
        'column' => 'Invitation',
        'sent-column' => 'Sent',
        'actions-label' => 'Invitation actions',
        'cancel' => 'Cancel',
        'cancel-confirm-title' => 'Cancel invitation?',
        'cancel-confirm-description' => 'The invitation link will stop working.',
        'cancel-confirm-label' => 'Cancel invitation',
        'cancelled' => 'Invitation cancelled.',
    ],

    'notification' => [
        'subject' => "You've been invited to join :team",
        'line' => ':inviter has invited you to join the :team team.',
        'action' => 'Accept invitation',
        'received-toast' => 'You have a new team invitation.',
        'removed-toast' => 'You were removed from a team.',
    ],

    'accept' => [
        'wrong-email' => 'This invitation was sent to a different email address.',
        'already-accepted' => 'This invitation has already been accepted.',
        'expired' => 'This invitation has expired.',
    ],
];
