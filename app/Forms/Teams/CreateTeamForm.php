<?php
declare(strict_types=1);

namespace App\Forms\Teams;

use App\Actions\Teams\CreateTeam;
use App\Concerns\ResolvesCurrentUser;
use Illuminate\Http\Request;
use Lattice\Facades\Effects;
use Lattice\Form\Attributes\AsForm;
use Lattice\Form\Components\Form as FormComponent;
use Lattice\Form\Components\TextInput;
use Lattice\Form\FormData;
use Lattice\Form\FormDefinition;
use Lattice\Http\LatticeResponse;
use Lattice\Ui\Components\Button;
use Lattice\Ui\Components\Grid;
use Lattice\Ui\Enums\HttpMethod;

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

    public function handle(FormData $data): LatticeResponse
    {
        $team = $this->createTeam->handle($this->currentUser(), $data->string('name')->toString());

        return Effects::respond()->toast(__('teams.create.created'))->toRoute('teams.edit', ['team' => $team->slug]);
    }
}
