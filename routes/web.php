<?php
declare(strict_types=1);

use App\Http\Controllers\Teams\AcceptInvitationController;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Lattice pages self-register their routes via #[AsPage]; only non-page routes live here.

Route::middleware(['auth'])->group(function () {
    Route::get('invitations/{invitation}/accept', AcceptInvitationController::class)->name('invitations.accept');
});

// Team-agnostic landing target: Fortify and bare /dashboard links bounce here to the current team's dashboard.
Route::middleware(['auth', 'verified'])->get('dashboard', function (Request $request) {
    $user = $request->user();
    $team = $user->currentTeam ?? $user->personalTeam();

    abort_unless($team instanceof Team, 403);

    return redirect()->route('dashboard', [...$request->query(), 'current_team' => $team->slug]);
})->name('dashboard.home');
