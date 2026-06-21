<?php
declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Language Lines
    |--------------------------------------------------------------------------
    */

    'failed' => 'Diese Anmeldedaten stimmen nicht mit unseren Aufzeichnungen überein.',
    'password' => 'Das angegebene Passwort ist falsch.',
    'throttle' => 'Zu viele Anmeldeversuche. Bitte versuche es in :seconds Sekunden erneut.',

    'login' => [
        'title' => 'Anmelden',
        'heading' => 'Bei deinem Konto anmelden',
        'subtitle' => 'Gib unten deine E-Mail-Adresse und dein Passwort ein, um dich anzumelden',
        'remember' => 'Angemeldet bleiben',
        'forgot-password' => 'Passwort vergessen?',
        'no-account' => 'Noch kein Konto?',
        'sign-up' => 'Registrieren',
    ],

    'register' => [
        'title' => 'Registrieren',
        'heading' => 'Konto erstellen',
        'subtitle' => 'Gib unten deine Daten ein, um dein Konto zu erstellen',
        'submit' => 'Konto erstellen',
        'have-account' => 'Bereits ein Konto?',
    ],

    'forgot-password' => [
        'title' => 'Passwort vergessen',
        'heading' => 'Passwort vergessen',
        'subtitle' => 'Gib deine E-Mail-Adresse ein, um einen Link zum Zurücksetzen des Passworts zu erhalten',
        'submit' => 'Link zum Zurücksetzen des Passworts senden',
        'return' => 'Oder zurück zur',
        'login-link' => 'Anmeldung',
    ],

    'reset-password' => [
        'title' => 'Passwort zurücksetzen',
        'heading' => 'Setze dein Passwort zurück',
        'subtitle' => 'Gib ein neues Passwort für dein Konto ein.',
        'submit' => 'Passwort zurücksetzen',
    ],

    'confirm-password' => [
        'title' => 'Passwort bestätigen',
        'heading' => 'Passwort bestätigen',
        'subtitle' => 'Dies ist ein geschützter Bereich der Anwendung. Bitte bestätige dein Passwort, bevor du fortfährst.',
        'submit' => 'Passwort bestätigen',
        'passkey-label' => 'Mit Passkey bestätigen',
        'passkey-loading' => 'Wird bestätigt …',
        'passkey-separator' => 'Oder mit Passwort bestätigen',
    ],

    'verify-email' => [
        'title' => 'E-Mail-Bestätigung',
        'heading' => 'E-Mail-Bestätigung',
        'subtitle' => 'Bitte bestätige deine E-Mail-Adresse, indem du auf den Link klickst, den wir dir gerade geschickt haben.',
        'resend' => 'Bestätigungs-E-Mail erneut senden',
        'sent' => 'Ein neuer Bestätigungslink wurde an die E-Mail-Adresse gesendet, die du bei der Registrierung angegeben hast.',
    ],

    'two-factor' => [
        'title' => 'Zwei-Faktor-Authentifizierung',
        'heading' => 'Zwei-Faktor-Authentifizierung',
        'subtitle' => 'Gib den Code aus deiner Authenticator-App ein, um fortzufahren',
        'continue' => 'Weiter',
        'code' => 'Authentifizierungscode',
        'recovery-code' => 'Wiederherstellungscode',
        'recovery-help' => 'Bestätige den Zugriff, indem du einen deiner Notfall-Wiederherstellungscodes eingibst.',
        'use-recovery' => 'Stattdessen einen Wiederherstellungscode verwenden',
    ],

];
