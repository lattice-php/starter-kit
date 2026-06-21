<?php
declare(strict_types=1);

namespace App\Pages\Auth;

use Illuminate\Http\Request;
use Lattice\Lattice\Attributes\AsPage;
use Lattice\Lattice\Core\Components\Button;
use Lattice\Lattice\Core\Components\Component;
use Lattice\Lattice\Core\Components\Grid;
use Lattice\Lattice\Core\Components\Heading;
use Lattice\Lattice\Core\Components\Stack;
use Lattice\Lattice\Core\Components\Text;
use Lattice\Lattice\Core\Enums\Align;
use Lattice\Lattice\Core\Enums\Gap;
use Lattice\Lattice\Core\Enums\HttpMethod;
use Lattice\Lattice\Core\Enums\PageContainer;
use Lattice\Lattice\Core\Enums\PageLayout;
use Lattice\Lattice\Core\PageSchema;
use Lattice\Lattice\Forms\Components\Form;
use Lattice\Lattice\Forms\Components\HiddenInput;
use Lattice\Lattice\Forms\Components\PasswordInput;
use Lattice\Lattice\Forms\Components\TextInput;
use Lattice\Lattice\Http\Page;

#[AsPage(layout: PageLayout::Auth, container: PageContainer::Default)]
class ResetPasswordPage extends Page
{
    public function title(): string
    {
        return __('auth.reset-password.title');
    }

    public function render(PageSchema $schema, Request $request): PageSchema
    {
        $token = (string) $request->route('token');
        $email = is_string($request->input('email')) ? $request->input('email') : '';

        return $schema->schema([
            Stack::make('reset-password-heading')
                ->gap(Gap::Small)
                ->schema([
                    Heading::make(__('auth.reset-password.heading'), 2),
                    Text::make(__('auth.reset-password.subtitle'))->align(Align::Center),
                ]),
            Form::make('reset-password-form')
                ->action(route('password.update', absolute: false))
                ->method(HttpMethod::Post)
                ->schema($this->formSchema($token, $email))
                ->resetOnSuccess(['password', 'password_confirmation'])
                ->withoutSubmitButton(),
        ]);
    }

    /**
     * @return array<int, Component>
     */
    private function formSchema(string $token, string $email): array
    {
        return [
            Grid::make('reset-password-fields')
                ->columns(1)
                ->schema([
                    HiddenInput::make('token', $token),
                    TextInput::make('email', __('common.field.email-address'))
                        ->email()
                        ->autoComplete('email')
                        ->value($email)
                        ->readOnly()
                        ->required(),
                    PasswordInput::make('password', __('common.field.password'))
                        ->autoComplete('new-password')
                        ->autoFocus()
                        ->placeholder(__('common.placeholder.password'))
                        ->required()
                        ->needsConfirmation(),
                ]),
            Button::make(__('auth.reset-password.submit'))->submit(),
        ];
    }
}
