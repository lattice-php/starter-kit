<?php
declare(strict_types=1);

namespace App\Forms\Teams;

use App\Actions\Teams\DeleteTeam;
use App\Concerns\ResolvesCurrentUser;
use App\Concerns\ResolvesTeamFromContext;
use App\Models\Team;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Lattice\Lattice\Attributes\AsForm;
use Lattice\Lattice\Core\Components\Button;
use Lattice\Lattice\Core\Components\Text;
use Lattice\Lattice\Core\Enums\ButtonVariant;
use Lattice\Lattice\Core\Enums\HttpMethod;
use Lattice\Lattice\Core\Enums\Variant;
use Lattice\Lattice\Forms\Components\Form as FormComponent;
use Lattice\Lattice\Forms\Components\TextInput;
use Lattice\Lattice\Forms\FormDefinition;
use Lattice\Lattice\Http\LatticeResponse;

#[AsForm('teams.delete')]
class DeleteTeamForm extends FormDefinition
{
    use ResolvesCurrentUser;
    use ResolvesTeamFromContext;

    public function __construct(private readonly DeleteTeam $deleteTeam) {}

    public function definition(FormComponent $form, Request $request): FormComponent
    {
        $team = $request->route('team');

        return $form
            ->method(HttpMethod::Delete)
            ->schema([
                Text::make('Please proceed with caution, this cannot be undone.'),
                TextInput::make('name', 'Confirm team name')
                    ->placeholder($team instanceof Team ? $team->name : '')
                    ->required()
                    ->rules(['string'])
                    ->rules(fn (): array => [function (string $attribute, mixed $value, Closure $fail): void {
                        if ((string) $value !== $this->teamFromContext()->name) {
                            $fail(__('The team name does not match.'));
                        }
                    }]),
                Button::make('Delete team')->submit()->variant(ButtonVariant::Destructive),
            ])
            ->withoutSubmitButton();
    }

    public function handle(Request $request): LatticeResponse
    {
        $team = $this->teamFromContext();

        Gate::authorize('delete', $team);

        $this->deleteTeam->handle($this->currentUser(), $team);

        return $this->toast(Variant::Success, __('Team deleted.'))->toRoute('teams.index');
    }
}
