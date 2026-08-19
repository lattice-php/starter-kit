<?php
declare(strict_types=1);

namespace App\Pages\Auth;

use App\Components\Auth\PasskeyVerify;
use Lattice\Core\Attributes\AsPage;
use Lattice\Core\Enums\PageLayout;
use Lattice\Form\Components\Form;
use Lattice\Form\Components\PasswordInput;
use Lattice\Http\Page;
use Lattice\Ui\Components\Button;
use Lattice\Ui\Components\Component;
use Lattice\Ui\Components\Grid;
use Lattice\Ui\Components\Heading;
use Lattice\Ui\Components\Stack;
use Lattice\Ui\Components\Text;
use Lattice\Ui\Enums\Align;
use Lattice\Ui\Enums\Gap;
use Lattice\Ui\Enums\HttpMethod;
use Lattice\Ui\PageSchema;

#[AsPage(layout: PageLayout::Auth)]
class ConfirmPasswordPage extends Page
{
    public function title(): string
    {
        return __('auth.confirm-password.title');
    }

    public function render(PageSchema $schema): PageSchema
    {
        return $schema->schema([
            Stack::make('confirm-password-heading')
                ->gap(Gap::Small)
                ->schema([
                    Heading::make(__('auth.confirm-password.heading'), 2),
                    Text::make(__('auth.confirm-password.subtitle'))
                        ->align(Align::Center),
                ]),
            PasskeyVerify::make(
                route('passkey.confirm-options', absolute: false),
                route('passkey.confirm', absolute: false),
            )
                ->label(__('auth.confirm-password.passkey-label'))
                ->loadingLabel(__('auth.confirm-password.passkey-loading'))
                ->separator(__('auth.confirm-password.passkey-separator')),
            Form::make('confirm-password-form')
                ->action(route('password.confirm.store', absolute: false))
                ->method(HttpMethod::Post)
                ->schema($this->formSchema())
                ->resetOnSuccess(['password'])
                ->withoutSubmitButton(),
        ]);
    }

    /**
     * @return array<int, Component>
     */
    private function formSchema(): array
    {
        return [
            Grid::make('confirm-password-fields')
                ->columns(1)
                ->schema([
                    PasswordInput::make('password', __('common.field.password'))
                        ->autoComplete('current-password')
                        ->autoFocus()
                        ->placeholder(__('common.placeholder.password'))
                        ->required(),
                ]),
            Button::make(__('auth.confirm-password.submit'))->submit(),
        ];
    }
}
