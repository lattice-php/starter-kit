<?php
declare(strict_types=1);

namespace App\Pages\Auth;

use App\Concerns\ResolvesFlashStatus;
use Illuminate\Http\Request;
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
use Lattice\Lattice\Forms\Components\TextInput;
use Lattice\Lattice\Http\Page;

#[AsPage(layout: PageLayout::Auth, container: PageContainer::Default)]
class ForgotPasswordPage extends Page
{
    use ResolvesFlashStatus;

    public function title(): string
    {
        return 'Forgot password';
    }

    public function render(PageSchema $schema, Request $request): PageSchema
    {
        return $schema->schema([
            Stack::make('forgot-password-heading')
                ->gap(Gap::Small)
                ->schema([
                    Heading::make('Forgot password', 2),
                    Text::make('Enter your email to receive a password reset link')
                        ->align(Align::Center),
                ]),
            Form::make('forgot-password-form')
                ->action(route('password.email', absolute: false))
                ->method(HttpMethod::Post)
                ->schema($this->formSchema())
                ->resetOnSuccess(['email'])
                ->withoutSubmitButton()
                ->status($this->flashStatus($request)),
        ]);
    }

    /**
     * @return array<int, Component>
     */
    private function formSchema(): array
    {
        return [
            Grid::make('forgot-password-fields')
                ->columns(1)
                ->schema([
                    TextInput::make('email', 'Email address')
                        ->email()
                        ->autoComplete('off')
                        ->autoFocus()
                        ->placeholder('email@example.com')
                        ->required(),
                ]),
            Button::make('Email password reset link')->submit(),
            Stack::make('forgot-password-login-prompt')
                ->align(Align::Center)
                ->direction('row')
                ->gap(Gap::ExtraSmall)
                ->schema([
                    Text::make('Or, return to'),
                    Link::make('log in')
                        ->href(route('login', absolute: false)),
                ]),
        ];
    }
}
