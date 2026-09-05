<?php
declare(strict_types=1);

namespace App\Providers;

use App\Models\Team;
use App\Models\TeamInvitation;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Laravel\Passkeys\Passkey;
use Lattice\Core\Facades\Lattice;
use Lattice\Core\Services\ContextResolutions;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->configureDefaults();
        $this->registerLatticeContext();
    }

    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }

    /**
     * Every Lattice definition reads its records through these keys instead of
     * route parameters, so a signed endpoint reached long after the page
     * request resolves them the same way the page did. The dependent keys
     * resolve inside their team, which is what keeps a forged member or
     * invitation id from reaching another team's records.
     */
    protected function registerLatticeContext(): void
    {
        Lattice::context('team', Team::class, by: 'slug');

        Lattice::context('member', function (string|int $value, array $context, ContextResolutions $resolutions): User {
            $team = $resolutions->resolve('team', $context['team'] ?? null, $context);

            abort_unless($team instanceof Team, 404);

            return $team->members()->findOrFail($value);
        }, keyBy: fn (User $member): int => $member->id);

        Lattice::context('invitation', function (string $value, array $context, ContextResolutions $resolutions): TeamInvitation {
            $team = $resolutions->resolve('team', $context['team'] ?? null, $context);

            abort_unless($team instanceof Team, 404);

            return $team->invitations()->where('code', $value)->firstOrFail();
        }, keyBy: fn (TeamInvitation $invitation): string => $invitation->code);

        Lattice::context('passkey', function (string|int $value, Request $request): Passkey {
            $user = $request->user();

            abort_unless($user instanceof User, 403);

            return $user->passkeys()->findOrFail($value);
        }, keyBy: fn (Passkey $passkey): int => $passkey->id);
    }
}
