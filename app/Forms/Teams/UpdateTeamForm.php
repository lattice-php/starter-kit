<?php
declare(strict_types=1);

namespace App\Forms\Teams;

use App\Concerns\ResolvesTeamFromContext;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Lattice\Facades\Effects;
use Lattice\Form\Attributes\AsForm;
use Lattice\Form\Components\Form as FormComponent;
use Lattice\Form\Components\TextInput;
use Lattice\Form\FormDefinition;
use Lattice\Http\LatticeResponse;
use Lattice\Ui\Components\Button;
use Lattice\Ui\Components\Grid;
use Lattice\Ui\Enums\HttpMethod;

#[AsForm('teams.update')]
class UpdateTeamForm extends FormDefinition
{
    use ResolvesTeamFromContext;

    public function definition(FormComponent $form, Request $request): FormComponent
    {
        $team = $request->route('team');

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
                Button::make(__('teams.update.submit'))->submit(),
            ])
            ->withoutSubmitButton();
    }

    public function handle(Request $request): LatticeResponse
    {
        $validated = $this->validate($request);

        $team = $this->teamFromContext();

        Gate::authorize('update', $team);

        $team = DB::transaction(function () use ($team, $validated): Team {
            $team = Team::whereKey($team->id)->lockForUpdate()->firstOrFail();

            $team->update(['name' => (string) $validated['name']]);

            return $team;
        });

        return Effects::respond()->toast(__('teams.update.updated'))->toRoute('teams.edit', ['team' => $team->slug]);
    }
}
