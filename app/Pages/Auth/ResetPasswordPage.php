<?php
declare(strict_types=1);

namespace App\Pages\Auth;

use Illuminate\Http\Request;
use Lattice\Core\Attributes\AsPage;
use Lattice\Core\Enums\PageLayout;
use Lattice\Form\Components\Form;
use Lattice\Form\Components\HiddenInput;
use Lattice\Form\Components\PasswordInput;
use Lattice\Form\Components\TextInput;
use Lattice\Http\Page;
use Lattice\Ui\Components\Button;
use Lattice\Ui\Components\Component;
use Lattice\Ui\Components\Grid;
use Lattice\Ui\Components\Heading;
use Lattice\Ui\Components\Stack;
use Lattice\Ui\Components\Text;
use Lattice\Ui\Enums\Gap;
use Lattice\Ui\Enums\HttpMethod;
use Lattice\Ui\Enums\TextAlign;
use Lattice\Ui\PageSchema;

#[AsPage(layout: PageLayout::Auth)]
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
                    Text::make(__('auth.reset-password.subtitle'))->align(TextAlign::Center),
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
