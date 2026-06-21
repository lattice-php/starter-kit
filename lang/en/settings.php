<?php
declare(strict_types=1);

return [
    'title' => 'Settings',
    'heading' => 'Settings',
    'subtitle' => 'Manage your profile, security, and appearance settings.',

    'tabs' => [
        'profile' => 'Profile',
        'security' => 'Security',
        'appearance' => 'Appearance',
    ],

    'profile' => [
        'heading' => 'Profile',
        'subtitle' => 'Update your name and email address.',
        'updated' => 'Profile updated.',
        'unverified' => 'Your email address is unverified.',
        'resend-verification' => 'Resend verification email',
        'verification-sent' => 'A new verification link has been sent to your email address.',
        'already-verified' => 'Your email address is already verified.',
    ],

    'security' => [
        'heading' => 'Security',
        'subtitle' => 'Update your password and manage sign-in security.',
    ],

    'password' => [
        'current' => 'Current password',
        'new' => 'New password',
        'updated' => 'Password updated.',
    ],

    'two-factor' => [
        'heading' => 'Two-factor authentication',
        'description-enabled' => 'You will be prompted for a secure one-time code during sign in.',
        'description-disabled' => 'Add an authenticator app code requirement to protect your account during sign in.',
        'enable' => 'Enable 2FA',
        'disable' => 'Disable 2FA',
        'disable-confirm-title' => 'Disable two-factor authentication?',
        'disable-confirm-description' => 'Your account will no longer require a one-time code during sign in.',
        'setup-title' => 'Set up two-factor authentication',
        'setup-description' => 'Scan the QR code with your authenticator app.',
        'setup-started' => 'Two-factor authentication setup started.',
        'setup-key' => 'Or enter this setup key in your authenticator app:',
        'already-enabled' => 'Two-factor authentication is enabled. You can close this dialog.',
        'confirm' => 'Confirm',
        'code' => 'Authentication code',
        'code-help' => 'Enter the code from your authenticator application.',
        'enabled-toast' => 'Two-factor authentication enabled.',
        'disabled-toast' => 'Two-factor authentication disabled.',
    ],

    'recovery-codes' => [
        'heading' => 'Recovery codes',
        'description' => 'Store these codes in a safe place. Each one can be used once to access your account if you lose your authenticator.',
        'regenerate' => 'Regenerate codes',
        'regenerate-confirm-title' => 'Regenerate recovery codes?',
        'regenerate-confirm-description' => 'Your existing recovery codes will stop working and be replaced with a new set.',
        'regenerated' => 'Recovery codes regenerated.',
    ],

    'passkeys' => [
        'heading' => 'Passkeys',
        'subtitle' => 'Manage your passkeys for passwordless sign-in.',
        'column' => 'Passkey',
        'authenticator' => 'Authenticator',
        'created' => 'Created',
        'last-used' => 'Last used',
        'added' => 'Added :time',
        'never-used' => 'Never used',
        'last-used-at' => 'Last used :time',
        'remove' => 'Remove passkey',
        'remove-confirm-title' => 'Remove passkey?',
        'remove-confirm-description' => 'You will no longer be able to use this passkey to sign in.',
        'removed' => 'Passkey removed.',
    ],

    'appearance' => [
        'heading' => 'Appearance settings',
        'subtitle' => 'Update the appearance settings for your account.',
        'label' => 'Appearance',
        'light' => 'Light',
        'dark' => 'Dark',
        'system' => 'System',
    ],

    'delete-account' => [
        'heading' => 'Delete account',
        'description' => 'Delete your account and all of its resources. This action cannot be undone. Enter your password to confirm.',
        'submit' => 'Delete account',
    ],
];
