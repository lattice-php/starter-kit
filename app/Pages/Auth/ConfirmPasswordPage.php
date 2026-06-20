<?php
declare(strict_types=1);

namespace App\Pages\Auth;

use App\Components\Auth\PasskeyVerify;
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
use Lattice\Lattice\Forms\Components\PasswordInput;
use Lattice\Lattice\Http\Page;

#[AsPage(layout: PageLayout::Auth, container: PageContainer::Default)]
class ConfirmPasswordPage extends Page
{
    public function title(): string
    {
        return 'Confirm password';
    }

    public function render(PageSchema $schema): PageSchema
    {
        return $schema->schema([
            Stack::make('confirm-password-heading')
                ->gap(Gap::Small)
                ->schema([
                    Heading::make('Confirm password', 2),
                    Text::make('This is a secure area of the application. Please confirm your password before continuing.')
                        ->align(Align::Center),
                ]),
            PasskeyVerify::make(
                route('passkey.confirm-options', absolute: false),
                route('passkey.confirm', absolute: false),
            )
                ->label('Confirm with passkey')
                ->loadingLabel('Confirming...')
                ->separator('Or confirm with password'),
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
                    PasswordInput::make('password', 'Password')
                        ->autoComplete('current-password')
                        ->autoFocus()
                        ->placeholder('Password')
                        ->required(),
                ]),
            Button::make('Confirm password')->submit(),
        ];
    }
}
