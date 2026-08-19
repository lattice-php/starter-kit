<?php
declare(strict_types=1);

namespace App\Pages\Auth;

use App\Components\Auth\PasskeyVerify;
use App\Concerns\ResolvesFlashStatus;
use Illuminate\Http\Request;
use Laravel\Fortify\Features;
use Lattice\Core\Attributes\AsPage;
use Lattice\Core\Enums\PageLayout;
use Lattice\Form\Components\Checkbox;
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
class LoginPage extends Page
{
    use ResolvesFlashStatus;

    public function title(): string
    {
        return __('auth.login.title');
    }

    public function render(PageSchema $schema, Request $request): PageSchema
    {
        $canResetPassword = Features::enabled(Features::resetPasswords());

        return $schema->schema([
            Stack::make('login-heading')
                ->gap(Gap::Small)
                ->schema([
                    Heading::make(__('auth.login.heading'), 2),
                    Text::make(__('auth.login.subtitle'))
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
                    TextInput::make('email', __('common.field.email-address'))
                        ->email()
                        ->autoComplete('email')
                        ->autoFocus()
                        ->placeholder(__('common.placeholder.email'))
                        ->required(),
                    $this->passwordInput($canResetPassword),
                    Checkbox::make('remember', __('auth.login.remember')),
                ]),
            Button::make(__('common.action.log-in'))->submit(),
            Stack::make('login-register-prompt')
                ->align(Align::Center)
                ->direction(StackDirection::Row)
                ->gap(Gap::ExtraSmall)
                ->schema([
                    Text::make(__('auth.login.no-account')),
                    Link::make(__('auth.login.sign-up'))
                        ->href(route('register', absolute: false)),
                ]),
        ];
    }

    private function passwordInput(bool $canResetPassword): PasswordInput
    {
        $input = PasswordInput::make('password', __('common.field.password'))
            ->autoComplete('current-password')
            ->placeholder(__('common.placeholder.password'))
            ->required();

        if (! $canResetPassword) {
            return $input;
        }

        return $input->labelAction(
            Link::make(__('auth.login.forgot-password'))->href(route('password.request', absolute: false)),
        );
    }
}
