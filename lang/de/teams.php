<?php
declare(strict_types=1);

return [
    'index' => [
        'title' => 'Teams',
        'heading' => 'Teams',
        'subtitle' => 'Verwalte deine Teams und Team-Mitgliedschaften.',
    ],

    'show' => [
        'subtitle' => 'Verwalte Team-Einstellungen, Mitglieder und Einladungen.',
        'details-heading' => 'Team-Details',
        'details-subtitle' => 'Aktualisiere den Namen deines Teams.',
        'invite-heading' => 'Mitglied einladen',
        'invite-subtitle' => 'Sende eine Einladung, um jemanden zu diesem Team hinzuzufügen.',
        'members-heading' => 'Team-Mitglieder',
        'members-subtitle' => 'Verwalte, wer zu diesem Team gehört.',
        'invitations-heading' => 'Ausstehende Einladungen',
        'invitations-subtitle' => 'Einladungen, die noch nicht angenommen wurden.',
    ],

    'fields' => [
        'name' => 'Team-Name',
    ],

    'columns' => [
        'team' => 'Team',
    ],

    'status' => [
        'personal' => 'Persönlich',
        'current' => 'Aktuell',
    ],

    'actions' => [
        'edit' => 'Bearbeiten',
    ],

    'roles' => [
        'owner' => 'Eigentümer',
        'admin' => 'Administrator',
        'member' => 'Mitglied',
    ],

    'create' => [
        'submit' => 'Team erstellen',
        'created' => 'Team erstellt.',
        'name-placeholder' => 'Mein Team',
    ],

    'update' => [
        'submit' => 'Team speichern',
        'updated' => 'Team aktualisiert.',
    ],

    'delete' => [
        'warning' => 'Bitte sei vorsichtig, dies kann nicht rückgängig gemacht werden.',
        'confirm-name' => 'Team-Namen bestätigen',
        'name-mismatch' => 'Der Team-Name stimmt nicht überein.',
        'submit' => 'Team löschen',
        'deleted' => 'Team gelöscht.',
    ],

    'invite' => [
        'submit' => 'Einladung senden',
        'sent' => 'Einladung gesendet.',
        'email-placeholder' => 'kollege@example.com',
        'already-member' => 'Diese E-Mail-Adresse ist bereits Mitglied oder hat eine ausstehende Einladung.',
    ],

    'switch' => [
        'label' => 'Team wechseln',
        'switched' => 'Zu :team gewechselt.',
    ],

    'members' => [
        'column' => 'Mitglied',
        'actions-label' => 'Mitglieder-Aktionen',
        'remove' => 'Entfernen',
        'remove-confirm-title' => 'Mitglied entfernen?',
        'remove-confirm-description' => 'Dieser Benutzer verliert den Zugriff auf das Team.',
        'remove-confirm-label' => 'Mitglied entfernen',
        'owner-cannot-be-removed' => 'Der Team-Eigentümer kann nicht entfernt werden.',
        'removed' => 'Mitglied entfernt.',
        'change-role' => 'Rolle ändern',
        'role-updated' => 'Mitglieder-Rolle aktualisiert.',
    ],

    'invitations' => [
        'column' => 'Einladung',
        'sent-column' => 'Gesendet',
        'actions-label' => 'Einladungs-Aktionen',
        'cancel' => 'Abbrechen',
        'cancel-confirm-title' => 'Einladung zurückziehen?',
        'cancel-confirm-description' => 'Der Einladungslink wird ungültig.',
        'cancel-confirm-label' => 'Einladung zurückziehen',
        'cancelled' => 'Einladung zurückgezogen.',
    ],

    'notification' => [
        'subject' => 'Du wurdest eingeladen, :team beizutreten',
        'line' => ':inviter hat dich eingeladen, dem Team :team beizutreten.',
        'action' => 'Einladung annehmen',
        'received-toast' => 'Du hast eine neue Team-Einladung.',
        'removed-toast' => 'Du wurdest aus einem Team entfernt.',
    ],

    'accept' => [
        'wrong-email' => 'Diese Einladung wurde an eine andere E-Mail-Adresse gesendet.',
        'already-accepted' => 'Diese Einladung wurde bereits angenommen.',
        'expired' => 'Diese Einladung ist abgelaufen.',
    ],
];
