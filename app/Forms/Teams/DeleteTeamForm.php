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
use Lattice\Lattice\Ui\Components\Button;
use Lattice\Lattice\Ui\Components\Text;
use Lattice\Lattice\Ui\Enums\ButtonVariant;
use Lattice\Lattice\Core\Enums\HttpMethod;
use Lattice\Lattice\Ui\Enums\Variant;
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
                Text::make(__('teams.delete.warning')),
                TextInput::make('name', __('teams.delete.confirm-name'))
                    ->placeholder($team instanceof Team ? $team->name : '')
                    ->required()
                    ->rules(['string'])
                    ->rules(fn (): array => [function (string $attribute, mixed $value, Closure $fail): void {
                        if ((string) $value !== $this->teamFromContext()->name) {
                            $fail(__('teams.delete.name-mismatch'));
                        }
                    }]),
                Button::make(__('teams.delete.submit'))->submit()->variant(ButtonVariant::Destructive),
            ])
            ->withoutSubmitButton();
    }

    public function handle(Request $request): LatticeResponse
    {
        $team = $this->teamFromContext();

        Gate::authorize('delete', $team);

        $this->deleteTeam->handle($this->currentUser(), $team);

        return $this->toast(Variant::Success, __('teams.delete.deleted'))->toRoute('teams.index');
    }
}
