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
use Lattice\Lattice\Ui\Enums\Align;
use Lattice\Lattice\Ui\Enums\Gap;
use Lattice\Lattice\Core\Enums\HttpMethod;
use Lattice\Lattice\Ui\Enums\PageContainer;
use Lattice\Lattice\Ui\Enums\PageLayout;
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
                ->direction('row')
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

        return $input->labelAction(__('auth.login.forgot-password'), route('password.request', absolute: false));
    }
}
