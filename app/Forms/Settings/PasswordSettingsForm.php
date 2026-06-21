<?php
declare(strict_types=1);

namespace App\Forms\Settings;

use App\Concerns\ResolvesCurrentUser;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;
use Lattice\Lattice\Attributes\AsForm;
use Lattice\Lattice\Core\Components\Button;
use Lattice\Lattice\Core\Components\Grid;
use Lattice\Lattice\Core\Enums\HttpMethod;
use Lattice\Lattice\Core\Enums\Variant;
use Lattice\Lattice\Forms\Components\Form;
use Lattice\Lattice\Forms\Components\PasswordInput;
use Lattice\Lattice\Forms\FormDefinition;
use Lattice\Lattice\Http\LatticeResponse;

#[AsForm('settings.password')]
class PasswordSettingsForm extends FormDefinition
{
    use ResolvesCurrentUser;

    public function definition(Form $form, Request $request): Form
    {
        return $form
            ->method(HttpMethod::Put)
            ->schema([
                Grid::make('password-fields')
                    ->columns(1)
                    ->schema([
                        PasswordInput::make('current_password', __('settings.password.current'))
                            ->autoComplete('current-password')
                            ->placeholder(__('settings.password.current'))
                            ->required()
                            ->rules(['current_password']),
                        PasswordInput::make('password', __('settings.password.new'))
                            ->autoComplete('new-password')
                            ->placeholder(__('settings.password.new'))
                            ->passwordRules(Password::defaults()->toPasswordRulesString())
                            ->needsConfirmation()
                            ->required(),
                    ]),
                Button::make(__('common.action.save'))->submit(),
            ])
            ->resetOnError(['password', 'password_confirmation', 'current_password'])
            ->resetOnSuccess()
            ->withoutSubmitButton();
    }

    public function handle(Request $request): LatticeResponse
    {
        $user = $this->currentUser();

        $validated = $this->validate($request);

        $user->update([
            'password' => $validated['password'],
        ]);

        return $this->toast(Variant::Success, __('settings.password.updated'))->back();
    }
}
