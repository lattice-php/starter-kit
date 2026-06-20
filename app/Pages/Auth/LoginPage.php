<?php
declare(strict_types=1);

namespace App\Pages\Auth;

use App\Components\Auth\PasskeyVerify;
use App\Concerns\ResolvesFlashStatus;
use Illuminate\Http\Request;
use Laravel\Fortify\Features;
use Lattice\Lattice\Attributes\AsPage;
use Lattice\Lattice\Core\Components\Button;
use Lattice\Lattice\Core\Components\Component;
use Lattice\Lattice\Core\Components\Grid;
use Lattice\Lattice\Core\Components\Heading;
use Lattice\Lattice\Core\Components\Link;
use Lattice\Lattice\Core\Components\Stack;
use Lattice\Lattice\Core\Components\Text;
use Lattice\Lattice\Core\Enums\Align;
use Lattice\Lattice\Core\Enums\Gap;
use Lattice\Lattice\Core\Enums\HttpMethod;
use Lattice\Lattice\Core\Enums\PageContainer;
use Lattice\Lattice\Core\Enums\PageLayout;
use Lattice\Lattice\Core\PageSchema;
use Lattice\Lattice\Forms\Components\Checkbox;
use Lattice\Lattice\Forms\Components\Form;
use Lattice\Lattice\Forms\Components\PasswordInput;
use Lattice\Lattice\Forms\Components\TextInput;
use Lattice\Lattice\Http\Page;

#[AsPage(layout: PageLayout::Auth, container: PageContainer::Default)]
class LoginPage extends Page
{
    use ResolvesFlashStatus;

    public function title(): string
    {
        return 'Log in';
    }

    public function render(PageSchema $schema, Request $request): PageSchema
    {
        $canResetPassword = Features::enabled(Features::resetPasswords());

        return $schema->schema([
            Stack::make('login-heading')
                ->gap(Gap::Small)
                ->schema([
                    Heading::make('Log in to your account', 2),
                    Text::make('Enter your email and password below to log in')
                        ->align(Align::Center),
                ]),
            PasskeyVerify::make(
                route('passkey.login-options', absolute: false),
                route('passkey.login', absolute: false),
            ),
            Form::make('login-form')
                ->action(route('login.store', absolute: false))
                ->method(HttpMethod::Post)
                ->schema($this->formSchema($canResetPassword))
                ->resetOnSuccess(['password'])
                ->withoutSubmitButton()
                ->status($this->flashStatus($request)),
        ]);
    }

    /**
     * @return array<int, Component>
     */
    private function formSchema(bool $canResetPassword): array
    {
        return [
            Grid::make('login-fields')
                ->columns(1)
                ->schema([
                    TextInput::make('email', 'Email address')
                        ->email()
                        ->autoComplete('email')
                        ->autoFocus()
                        ->placeholder('email@example.com')
                        ->required(),
                    $this->passwordInput($canResetPassword),
                    Checkbox::make('remember', 'Remember me'),
                ]),
            Button::make('Log in')->submit(),
            Stack::make('login-register-prompt')
                ->align(Align::Center)
                ->direction('row')
                ->gap(Gap::ExtraSmall)
                ->schema([
                    Text::make("Don't have an account?"),
                    Link::make('Sign up')
                        ->href(route('register', absolute: false)),
                ]),
        ];
    }

    private function passwordInput(bool $canResetPassword): PasswordInput
    {
        $input = PasswordInput::make('password', 'Password')
            ->autoComplete('current-password')
            ->placeholder('Password')
            ->required();

        if (! $canResetPassword) {
            return $input;
        }

        return $input->labelAction('Forgot your password?', route('password.request', absolute: false));
    }
}
