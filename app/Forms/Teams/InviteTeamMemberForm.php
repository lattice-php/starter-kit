<?php
declare(strict_types=1);

namespace App\Forms\Teams;

use App\Concerns\ResolvesCurrentUser;
use App\Concerns\ResolvesTeamFromContext;
use App\Enums\TeamRole;
use App\Notifications\Teams\TeamInvitation;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Notification;
use Lattice\Lattice\Attributes\AsForm;
use Lattice\Lattice\Core\Components\Button;
use Lattice\Lattice\Core\Components\Grid;
use Lattice\Lattice\Core\Enums\HttpMethod;
use Lattice\Lattice\Ui\Enums\Variant;
use Lattice\Lattice\Forms\Components\Choice;
use Lattice\Lattice\Forms\Components\Form as FormComponent;
use Lattice\Lattice\Forms\Components\TextInput;
use Lattice\Lattice\Forms\FormDefinition;
use Lattice\Lattice\Http\LatticeResponse;

#[AsForm('teams.invite')]
class InviteTeamMemberForm extends FormDefinition
{
    use ResolvesCurrentUser;
    use ResolvesTeamFromContext;

    public function definition(FormComponent $form, Request $request): FormComponent
    {
        return $form
            ->method(HttpMethod::Post)
            ->resetOnSuccess(['email'])
            ->schema([
                Grid::make('teams-invite-fields')
                    ->columns(1)
                    ->schema([
                        TextInput::make('email', __('common.field.email-address'))
                            ->email()
                            ->placeholder(__('teams.invite.email-placeholder'))
                            ->required()
                            ->rules(['string', 'max:255'])
                            ->rules(fn (): array => [function (string $attribute, mixed $value, Closure $fail): void {
                                if ($this->teamFromContext()->hasMemberOrPendingInvitation((string) $value)) {
                                    $fail(__('teams.invite.already-member'));
                                }
                            }]),
                        Choice::make('role', __('common.field.role'))
                            ->value(TeamRole::Member->value)
                            ->enum(TeamRole::assignableCases())
                            ->required(),
                    ]),
                Button::make(__('teams.invite.submit'))->submit(),
            ])
            ->withoutSubmitButton();
    }

    public function handle(Request $request): LatticeResponse
    {
        $team = $this->teamFromContext();

        Gate::authorize('inviteMember', $team);

        $validated = $this->validate($request);

        $invitation = $team->invitations()->create([
            'email' => (string) $validated['email'],
            'role' => TeamRole::from((string) $validated['role']),
            'invited_by' => $this->currentUser()->id,
            'expires_at' => now()->addDays(3),
        ]);

        Notification::route('mail', $invitation->email)
            ->notify(new TeamInvitation($invitation));

        return $this->toast(Variant::Success, __('teams.invite.sent'))->toRoute('teams.edit', ['team' => $team->slug]);
    }
}
