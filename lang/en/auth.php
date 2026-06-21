<?php
declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Language Lines
    |--------------------------------------------------------------------------
    */

    'failed' => 'These credentials do not match our records.',
    'password' => 'The provided password is incorrect.',
    'throttle' => 'Too many login attempts. Please try again in :seconds seconds.',

    'login' => [
        'title' => 'Log in',
        'heading' => 'Log in to your account',
        'subtitle' => 'Enter your email and password below to log in',
        'remember' => 'Remember me',
        'forgot-password' => 'Forgot your password?',
        'no-account' => "Don't have an account?",
        'sign-up' => 'Sign up',
    ],

    'register' => [
        'title' => 'Register',
        'heading' => 'Create an account',
        'subtitle' => 'Enter your details below to create your account',
        'submit' => 'Create account',
        'have-account' => 'Already have an account?',
    ],

    'forgot-password' => [
        'title' => 'Forgot password',
        'heading' => 'Forgot password',
        'subtitle' => 'Enter your email to receive a password reset link',
        'submit' => 'Email password reset link',
        'return' => 'Or, return to',
        'login-link' => 'log in',
    ],

    'reset-password' => [
        'title' => 'Reset password',
        'heading' => 'Reset your password',
        'subtitle' => 'Enter a new password for your account.',
        'submit' => 'Reset password',
    ],

    'confirm-password' => [
        'title' => 'Confirm password',
        'heading' => 'Confirm password',
        'subtitle' => 'This is a secure area of the application. Please confirm your password before continuing.',
        'submit' => 'Confirm password',
        'passkey-label' => 'Confirm with passkey',
        'passkey-loading' => 'Confirming...',
        'passkey-separator' => 'Or confirm with password',
    ],

    'verify-email' => [
        'title' => 'Email verification',
        'heading' => 'Email verification',
        'subtitle' => 'Please verify your email address by clicking on the link we just emailed to you.',
        'resend' => 'Resend verification email',
        'sent' => 'A new verification link has been sent to the email address you provided during registration.',
    ],

    'two-factor' => [
        'title' => 'Two-factor authentication',
        'heading' => 'Two-factor authentication',
        'subtitle' => 'Enter the code from your authenticator app to continue',
        'continue' => 'Continue',
        'code' => 'Authentication code',
        'recovery-code' => 'Recovery code',
        'recovery-help' => 'Confirm access by entering one of your emergency recovery codes.',
        'use-recovery' => 'Use a recovery code instead',
    ],

];
