<?php
declare(strict_types=1);

namespace App\Forms\Teams;

use App\Actions\Teams\DeleteTeam;
use App\Concerns\ResolvesCurrentUser;
use App\Models\Team;
use Closure;
use Illuminate\Http\Request;
use Lattice\Facades\Effects;
use Lattice\Form\Attributes\AsForm;
use Lattice\Form\Components\Form as FormComponent;
use Lattice\Form\Components\TextInput;
use Lattice\Form\FormDefinition;
use Lattice\Http\LatticeResponse;
use Lattice\Ui\Components\Button;
use Lattice\Ui\Components\Text;
use Lattice\Ui\Enums\HttpMethod;
use Lattice\Ui\Enums\Variant;

#[AsForm('teams.delete', can: 'delete', on: 'team')]
class DeleteTeamForm extends FormDefinition
{
    use ResolvesCurrentUser;

    public function __construct(private readonly DeleteTeam $deleteTeam) {}

    public function definition(FormComponent $form, Request $request): FormComponent
    {
        $team = $this->contextModelOrNull('team');

        return $form
            ->method(HttpMethod::Delete)
            ->schema([
                Text::make(__('teams.delete.warning')),
                TextInput::make('name', __('teams.delete.confirm-name'))
                    ->placeholder($team instanceof Team ? $team->name : '')
                    ->required()
                    ->rules(['string'])
                    ->rules(fn (): array => [function (string $attribute, mixed $value, Closure $fail): void {
                        /** @var Team $team */
                        $team = $this->contextModel('team');

                        if ((string) $value !== $team->name) {
                            $fail(__('teams.delete.name-mismatch'));
                        }
                    }]),
                Button::make(__('teams.delete.submit'))->submit()->variant(Variant::Danger),
            ])
            ->withoutSubmitButton();
    }

    public function handle(): LatticeResponse
    {
        /** @var Team $team */
        $team = $this->contextModel('team');

        $this->deleteTeam->handle($this->currentUser(), $team);

        return Effects::respond()->toast(__('teams.delete.deleted'))->toRoute('teams.index');
    }
}
