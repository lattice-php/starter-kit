<?php
declare(strict_types=1);

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Pages\Auth\ConfirmPasswordPage;
use App\Pages\Auth\ForgotPasswordPage;
use App\Pages\Auth\LoginPage;
use App\Pages\Auth\RegisterPage;
use App\Pages\Auth\ResetPasswordPage;
use App\Pages\Auth\TwoFactorChallengePage;
use App\Pages\Auth\VerifyEmailPage;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Passkey logins reuse Fortify's home so they funnel through the team-agnostic /dashboard landing route.
        config(['passkeys.redirect' => config('fortify.home')]);

        $this->configureActions();
        $this->configureViews();
        $this->configureRateLimiting();
    }

    private function configureActions(): void
    {
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);
        Fortify::createUsersUsing(CreateNewUser::class);
    }

    private function configureViews(): void
    {
        Fortify::loginView(fn () => new LoginPage);
        Fortify::resetPasswordView(fn () => new ResetPasswordPage);
        Fortify::requestPasswordResetLinkView(fn () => new ForgotPasswordPage);
        Fortify::verifyEmailView(fn () => new VerifyEmailPage);
        Fortify::registerView(fn () => new RegisterPage);
        Fortify::twoFactorChallengeView(fn () => new TwoFactorChallengePage);
        Fortify::confirmPasswordView(fn () => new ConfirmPasswordPage);
    }

    private function configureRateLimiting(): void
    {
        RateLimiter::for('two-factor', fn (Request $request) => Limit::perMinute(5)->by($request->session()->get('login.id')));

        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())).'|'.$request->ip());

            return Limit::perMinute(5)->by($throttleKey);
        });

        RateLimiter::for('passkeys', function (Request $request) {
            $credentialId = $request->input('credential.id');

            return Limit::perMinute(10)->by(
                ($credentialId ?: $request->session()->getId()).'|'.$request->ip(),
            );
        });
    }
}
