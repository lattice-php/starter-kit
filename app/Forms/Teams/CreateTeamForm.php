<?php
declare(strict_types=1);

namespace App\Forms\Teams;

use App\Actions\Teams\CreateTeam;
use App\Concerns\ResolvesCurrentUser;
use Illuminate\Http\Request;
use Lattice\Lattice\Attributes\AsForm;
use Lattice\Lattice\Core\Components\Button;
use Lattice\Lattice\Core\Components\Grid;
use Lattice\Lattice\Core\Enums\HttpMethod;
use Lattice\Lattice\Ui\Enums\Variant;
use Lattice\Lattice\Forms\Components\Form as FormComponent;
use Lattice\Lattice\Forms\Components\TextInput;
use Lattice\Lattice\Forms\FormDefinition;
use Lattice\Lattice\Http\LatticeResponse;

#[AsForm('teams.create')]
class CreateTeamForm extends FormDefinition
{
    use ResolvesCurrentUser;

    public function __construct(private readonly CreateTeam $createTeam) {}

    public function definition(FormComponent $form, Request $request): FormComponent
    {
        return $form
            ->method(HttpMethod::Post)
            ->schema([
                Grid::make('teams-create-fields')
                    ->columns(1)
                    ->schema([
                        TextInput::make('name', __('teams.fields.name'))
                            ->placeholder(__('teams.create.name-placeholder'))
                            ->required()
                            ->rules(['string', 'max:255']),
                    ]),
                Button::make(__('teams.create.submit'))->submit(),
            ])
            ->withoutSubmitButton();
    }

    public function handle(Request $request): LatticeResponse
    {
        $validated = $this->validate($request);

        $team = $this->createTeam->handle($this->currentUser(), (string) $validated['name']);

        return $this->toast(Variant::Success, __('teams.create.created'))->toRoute('teams.edit', ['team' => $team->slug]);
    }
}
