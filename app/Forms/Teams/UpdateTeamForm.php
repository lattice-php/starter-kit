<?php
declare(strict_types=1);

namespace App\Forms\Teams;

use App\Concerns\ResolvesTeamFromContext;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Lattice\Lattice\Attributes\AsForm;
use Lattice\Lattice\Core\Components\Button;
use Lattice\Lattice\Core\Components\Grid;
use Lattice\Lattice\Core\Enums\HttpMethod;
use Lattice\Lattice\Ui\Enums\Variant;
use Lattice\Lattice\Forms\Components\Form as FormComponent;
use Lattice\Lattice\Forms\Components\TextInput;
use Lattice\Lattice\Forms\FormDefinition;
use Lattice\Lattice\Http\LatticeResponse;

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

        return $this->toast(Variant::Success, __('teams.update.updated'))->toRoute('teams.edit', ['team' => $team->slug]);
    }
}
