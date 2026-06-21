<?php
declare(strict_types=1);

return [
    'title' => 'Einstellungen',
    'heading' => 'Einstellungen',
    'subtitle' => 'Verwalte deine Profil-, Sicherheits- und Darstellungseinstellungen.',

    'tabs' => [
        'profile' => 'Profil',
        'security' => 'Sicherheit',
        'appearance' => 'Darstellung',
    ],

    'profile' => [
        'heading' => 'Profil',
        'subtitle' => 'Aktualisiere deinen Namen und deine E-Mail-Adresse.',
        'updated' => 'Profil aktualisiert.',
        'unverified' => 'Deine E-Mail-Adresse ist nicht bestätigt.',
        'resend-verification' => 'Bestätigungs-E-Mail erneut senden',
        'verification-sent' => 'Ein neuer Bestätigungslink wurde an deine E-Mail-Adresse gesendet.',
        'already-verified' => 'Deine E-Mail-Adresse ist bereits bestätigt.',
    ],

    'security' => [
        'heading' => 'Sicherheit',
        'subtitle' => 'Aktualisiere dein Passwort und verwalte die Anmeldesicherheit.',
    ],

    'password' => [
        'current' => 'Aktuelles Passwort',
        'new' => 'Neues Passwort',
        'updated' => 'Passwort aktualisiert.',
    ],

    'two-factor' => [
        'heading' => 'Zwei-Faktor-Authentifizierung',
        'description-enabled' => 'Bei der Anmeldung wirst du nach einem sicheren Einmalcode gefragt.',
        'description-disabled' => 'Füge die Abfrage eines Authenticator-App-Codes hinzu, um dein Konto bei der Anmeldung zu schützen.',
        'enable' => '2FA aktivieren',
        'disable' => '2FA deaktivieren',
        'disable-confirm-title' => 'Zwei-Faktor-Authentifizierung deaktivieren?',
        'disable-confirm-description' => 'Dein Konto erfordert bei der Anmeldung keinen Einmalcode mehr.',
        'setup-title' => 'Zwei-Faktor-Authentifizierung einrichten',
        'setup-description' => 'Scanne den QR-Code mit deiner Authenticator-App.',
        'setup-started' => 'Einrichtung der Zwei-Faktor-Authentifizierung gestartet.',
        'setup-key' => 'Oder gib diesen Einrichtungsschlüssel in deiner Authenticator-App ein:',
        'already-enabled' => 'Die Zwei-Faktor-Authentifizierung ist aktiviert. Du kannst diesen Dialog schließen.',
        'confirm' => 'Bestätigen',
        'code' => 'Authentifizierungscode',
        'code-help' => 'Gib den Code aus deiner Authenticator-App ein.',
        'enabled-toast' => 'Zwei-Faktor-Authentifizierung aktiviert.',
        'disabled-toast' => 'Zwei-Faktor-Authentifizierung deaktiviert.',
    ],

    'recovery-codes' => [
        'heading' => 'Wiederherstellungscodes',
        'description' => 'Bewahre diese Codes an einem sicheren Ort auf. Jeder kann einmal verwendet werden, um auf dein Konto zuzugreifen, falls du deinen Authenticator verlierst.',
        'regenerate' => 'Codes neu generieren',
        'regenerate-confirm-title' => 'Wiederherstellungscodes neu generieren?',
        'regenerate-confirm-description' => 'Deine bestehenden Wiederherstellungscodes werden ungültig und durch einen neuen Satz ersetzt.',
        'regenerated' => 'Wiederherstellungscodes neu generiert.',
    ],

    'passkeys' => [
        'heading' => 'Passkeys',
        'subtitle' => 'Verwalte deine Passkeys für die passwortlose Anmeldung.',
        'column' => 'Passkey',
        'authenticator' => 'Authenticator',
        'created' => 'Erstellt',
        'last-used' => 'Zuletzt verwendet',
        'added' => 'Hinzugefügt :time',
        'never-used' => 'Nie verwendet',
        'last-used-at' => 'Zuletzt verwendet :time',
        'remove' => 'Passkey entfernen',
        'remove-confirm-title' => 'Passkey entfernen?',
        'remove-confirm-description' => 'Du kannst diesen Passkey dann nicht mehr zur Anmeldung verwenden.',
        'removed' => 'Passkey entfernt.',
    ],

    'appearance' => [
        'heading' => 'Darstellungseinstellungen',
        'subtitle' => 'Aktualisiere die Darstellungseinstellungen für dein Konto.',
        'label' => 'Darstellung',
        'light' => 'Hell',
        'dark' => 'Dunkel',
        'system' => 'System',
    ],

    'delete-account' => [
        'heading' => 'Konto löschen',
        'description' => 'Lösche dein Konto und alle zugehörigen Ressourcen. Diese Aktion kann nicht rückgängig gemacht werden. Gib dein Passwort zur Bestätigung ein.',
        'submit' => 'Konto löschen',
    ],
];
