<?php
declare(strict_types=1);

namespace App\Forms\Settings;

use App\Concerns\ResolvesCurrentUser;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Lattice\Form\Attributes\AsForm;
use Lattice\Form\Components\Form;
use Lattice\Form\Components\PasswordInput;
use Lattice\Form\FormDefinition;
use Lattice\Ui\Components\Button;
use Lattice\Ui\Components\Heading;
use Lattice\Ui\Components\Stack;
use Lattice\Ui\Components\Text;
use Lattice\Ui\Enums\HttpMethod;
use Lattice\Ui\Enums\Orientation;
use Lattice\Ui\Enums\Variant;

#[AsForm('settings.delete-account')]
class DeleteAccountForm extends FormDefinition
{
    use ResolvesCurrentUser;

    public function definition(Form $form, Request $request): Form
    {
        return $form
            ->method(HttpMethod::Delete)
            ->schema([
                Heading::make(__('settings.delete-account.heading'), 2),
                Text::make(__('settings.delete-account.description')),
                PasswordInput::make('password', __('common.field.password'))
                    ->autoComplete('current-password')
                    ->placeholder(__('common.placeholder.password'))
                    ->required()
                    ->rules(['current_password']),
                Stack::make('delete-account-actions')
                    ->direction(Orientation::Horizontal)
                    ->schema([
                        Button::make(__('settings.delete-account.submit'))->submit()->variant(Variant::Danger),
                    ]),
            ])
            ->resetOnSuccess()
            ->withoutSubmitButton();
    }

    public function handle(Request $request): RedirectResponse
    {
        $user = $this->currentUser();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
