<?php
declare(strict_types=1);

namespace App\Forms\Teams;

use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

#[AsForm('teams.update', can: 'update', on: 'team')]
class UpdateTeamForm extends FormDefinition
{
    public function definition(FormComponent $form, Request $request): FormComponent
    {
        $team = $this->contextModelOrNull('team');

        return $form
            ->method(HttpMethod::Patch)
            ->schema([
                Grid::make('teams-update-fields')
                    ->columns(1)
                    ->schema([
                        TextInput::make('name', __('teams.fields.name'))
                            ->value($team instanceof Team ? $team->name : null)
                            ->placeholder(__('teams.fields.name'))
                            ->required()
                            ->rules(['string', 'max:255']),
                    ]),
                Button::make(__('common.action.save'))->submit(),
            ])
            ->withoutSubmitButton();
    }

    public function handle(FormData $data): LatticeResponse
    {
        /** @var Team $team */
        $team = $this->contextModel('team');

        $team = DB::transaction(function () use ($team, $data): Team {
            $locked = Team::whereKey($team->id)->lockForUpdate()->firstOrFail();

            $locked->update(['name' => $data->string('name')->toString()]);

            return $locked;
        });

        return Effects::respond()->toast(__('teams.update.updated'))->toRoute('teams.edit', ['team' => $team->slug]);
    }
}
