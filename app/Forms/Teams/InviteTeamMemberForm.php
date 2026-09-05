<?php
declare(strict_types=1);

namespace App\Forms\Teams;

use App\Concerns\ResolvesCurrentUser;
use App\Enums\TeamRole;
use App\Models\Team;
use App\Notifications\Teams\TeamInvitation;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Lattice\Facades\Effects;
use Lattice\Form\Attributes\AsForm;
use Lattice\Form\Components\Choice;
use Lattice\Form\Components\Form as FormComponent;
use Lattice\Form\Components\TextInput;
use Lattice\Form\FormData;
use Lattice\Form\FormDefinition;
use Lattice\Http\LatticeResponse;
use Lattice\Ui\Components\Button;
use Lattice\Ui\Components\Grid;
use Lattice\Ui\Enums\HttpMethod;

#[AsForm('teams.invite', can: 'inviteMember', on: 'team')]
class InviteTeamMemberForm extends FormDefinition
{
    use ResolvesCurrentUser;

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
                                /** @var Team $team */
                                $team = $this->contextModel('team');

                                if ($team->hasMemberOrPendingInvitation((string) $value)) {
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

    public function handle(FormData $data): LatticeResponse
    {
        /** @var Team $team */
        $team = $this->contextModel('team');

        $invitation = $team->invitations()->create([
            'email' => $data->string('email')->toString(),
            'role' => $data->enum('role', TeamRole::class),
            'invited_by' => $this->currentUser()->id,
            'expires_at' => now()->addDays(3),
        ]);

        Notification::route('mail', $invitation->email)
            ->notify(new TeamInvitation($invitation));

        return Effects::respond()->toast(__('teams.invite.sent'))->toRoute('teams.edit', ['team' => $team->slug]);
    }
}
