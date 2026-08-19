<?php
declare(strict_types=1);

namespace App\Pages\Auth;

use Lattice\Core\Attributes\AsPage;
use Lattice\Core\Enums\PageLayout;
use Lattice\Form\Components\Form;
use Lattice\Form\Components\PasswordInput;
use Lattice\Form\Components\TextInput;
use Lattice\Http\Page;
use Lattice\Ui\Components\Button;
use Lattice\Ui\Components\Component;
use Lattice\Ui\Components\Grid;
use Lattice\Ui\Components\Heading;
use Lattice\Ui\Components\Link;
use Lattice\Ui\Components\Stack;
use Lattice\Ui\Components\Text;
use Lattice\Ui\Enums\Align;
use Lattice\Ui\Enums\Gap;
use Lattice\Ui\Enums\HttpMethod;
use Lattice\Ui\Enums\StackDirection;
use Lattice\Ui\PageSchema;

#[AsPage(layout: PageLayout::Auth)]
class RegisterPage extends Page
{
    public function title(): string
    {
        return __('auth.register.title');
    }

    public function render(PageSchema $schema): PageSchema
    {
        return $schema->schema([
            Stack::make('register-heading')
                ->gap(Gap::Small)
                ->schema([
                    Heading::make(__('auth.register.heading'), 2),
                    Text::make(__('auth.register.subtitle'))
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
                    TextInput::make('name', __('common.field.name'))
                        ->autoComplete('name')
                        ->autoFocus()
                        ->placeholder(__('common.placeholder.full-name'))
                        ->required(),
                    TextInput::make('email', __('common.field.email-address'))
                        ->email()
                        ->autoComplete('email')
                        ->placeholder(__('common.placeholder.email'))
                        ->required(),
                    PasswordInput::make('password', __('common.field.password'))
                        ->autoComplete('new-password')
                        ->placeholder(__('common.placeholder.password'))
                        ->required()
                        ->needsConfirmation(),
                ]),
            Button::make(__('auth.register.submit'))->submit(),
            Stack::make('register-login-prompt')
                ->align(Align::Center)
                ->direction(StackDirection::Row)
                ->gap(Gap::ExtraSmall)
                ->schema([
                    Text::make(__('auth.register.have-account')),
                    Link::make(__('common.action.log-in'))
                        ->href(route('login', absolute: false)),
                ]),
        ];
    }
}
