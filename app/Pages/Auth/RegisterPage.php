<?php
declare(strict_types=1);

namespace App\Pages\Auth;

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
use Lattice\Lattice\Forms\Components\Form;
use Lattice\Lattice\Forms\Components\PasswordInput;
use Lattice\Lattice\Forms\Components\TextInput;
use Lattice\Lattice\Http\Page;

#[AsPage(layout: PageLayout::Auth, container: PageContainer::Default)]
class RegisterPage extends Page
{
    public function title(): string
    {
        return 'Register';
    }

    public function render(PageSchema $schema): PageSchema
    {
        return $schema->schema([
            Stack::make('register-heading')
                ->gap(Gap::Small)
                ->schema([
                    Heading::make('Create an account', 2),
                    Text::make('Enter your details below to create your account')
                        ->align(Align::Center),
                ]),
            Form::make('register-form')
                ->action(route('register.store', absolute: false))
                ->method(HttpMethod::Post)
                ->schema($this->formSchema())
                ->resetOnSuccess(['password', 'password_confirmation'])
                ->withoutSubmitButton(),
        ]);
    }

    /**
     * @return array<int, Component>
     */
    private function formSchema(): array
    {
        return [
            Grid::make('register-fields')
                ->columns(1)
                ->schema([
                    TextInput::make('name', 'Name')
                        ->autoComplete('name')
                        ->autoFocus()
                        ->placeholder('Full name')
                        ->required(),
                    TextInput::make('email', 'Email address')
                        ->email()
                        ->autoComplete('email')
                        ->placeholder('email@example.com')
                        ->required(),
                    PasswordInput::make('password', 'Password')
                        ->autoComplete('new-password')
                        ->placeholder('Password')
                        ->required()
                        ->needsConfirmation(),
                ]),
            Button::make('Create account')->submit(),
            Stack::make('register-login-prompt')
                ->align(Align::Center)
                ->direction('row')
                ->gap(Gap::ExtraSmall)
                ->schema([
                    Text::make('Already have an account?'),
                    Link::make('Log in')
                        ->href(route('login', absolute: false)),
                ]),
        ];
    }
}
