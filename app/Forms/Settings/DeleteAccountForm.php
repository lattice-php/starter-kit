<?php
declare(strict_types=1);

namespace App\Forms\Settings;

use App\Concerns\ResolvesCurrentUser;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Lattice\Lattice\Attributes\AsForm;
use Lattice\Lattice\Ui\Components\Button;
use Lattice\Lattice\Ui\Components\Heading;
use Lattice\Lattice\Ui\Components\Stack;
use Lattice\Lattice\Ui\Components\Text;
use Lattice\Lattice\Ui\Enums\ButtonVariant;
use Lattice\Lattice\Core\Enums\HttpMethod;
use Lattice\Lattice\Forms\Components\Form;
use Lattice\Lattice\Forms\Components\PasswordInput;
use Lattice\Lattice\Forms\FormDefinition;

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
                    ->direction('row')
                    ->schema([
                        Button::make(__('settings.delete-account.submit'))->submit()->variant(ButtonVariant::Destructive),
                    ]),
            ])
            ->resetOnSuccess()
            ->withoutSubmitButton();
    }

    public function handle(Request $request): RedirectResponse
    {
        $user = $this->currentUser();

        $this->validate($request);

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
