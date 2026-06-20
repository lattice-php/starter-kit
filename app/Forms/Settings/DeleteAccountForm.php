<?php
declare(strict_types=1);

namespace App\Forms\Settings;

use App\Concerns\ResolvesCurrentUser;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Lattice\Lattice\Attributes\AsForm;
use Lattice\Lattice\Core\Components\Button;
use Lattice\Lattice\Core\Components\Heading;
use Lattice\Lattice\Core\Components\Stack;
use Lattice\Lattice\Core\Components\Text;
use Lattice\Lattice\Core\Enums\ButtonVariant;
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
                Heading::make('Delete account', 2),
                Text::make('Delete your account and all of its resources. This action cannot be undone. Enter your password to confirm.'),
                PasswordInput::make('password', 'Password')
                    ->autoComplete('current-password')
                    ->placeholder('Password')
                    ->required()
                    ->rules(['current_password']),
                Stack::make('delete-account-actions')
                    ->direction('row')
                    ->schema([
                        Button::make('Delete account')->submit()->variant(ButtonVariant::Destructive),
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
