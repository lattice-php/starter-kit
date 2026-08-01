<?php
declare(strict_types=1);

namespace App\Pages\Auth;

use App\Concerns\ResolvesFlashStatus;
use Illuminate\Http\Request;
use Lattice\Lattice\Attributes\AsPage;
use Lattice\Lattice\Core\PageSchema;
use Lattice\Lattice\Forms\Components\Form;
use Lattice\Lattice\Forms\Components\TextInput;
use Lattice\Lattice\Http\Page;
use Lattice\Lattice\Ui\Components\Button;
use Lattice\Lattice\Ui\Components\Component;
use Lattice\Lattice\Ui\Components\Grid;
use Lattice\Lattice\Ui\Components\Heading;
use Lattice\Lattice\Ui\Components\Link;
use Lattice\Lattice\Ui\Components\Stack;
use Lattice\Lattice\Ui\Components\Text;
use Lattice\Lattice\Ui\Enums\Align;
use Lattice\Lattice\Ui\Enums\Gap;
use Lattice\Lattice\Ui\Enums\HttpMethod;
use Lattice\Lattice\Ui\Enums\PageContainer;
use Lattice\Lattice\Ui\Enums\PageLayout;
use Lattice\Lattice\Ui\Enums\StackDirection;

#[AsPage(layout: PageLayout::Auth, container: PageContainer::Default)]
class ForgotPasswordPage extends Page
{
    use ResolvesFlashStatus;

    public function title(): string
    {
        return __('auth.forgot-password.title');
    }

    public function render(PageSchema $schema, Request $request): PageSchema
    {
        return $schema->schema([
            Stack::make('forgot-password-heading')
                ->gap(Gap::Small)
                ->schema([
                    Heading::make(__('auth.forgot-password.heading'), 2),
                    Text::make(__('auth.forgot-password.subtitle'))
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
                    TextInput::make('email', __('common.field.email-address'))
                        ->email()
                        ->autoComplete('off')
                        ->autoFocus()
                        ->placeholder(__('common.placeholder.email'))
                        ->required(),
                ]),
            Button::make(__('auth.forgot-password.submit'))->submit(),
            Stack::make('forgot-password-login-prompt')
                ->align(Align::Center)
                ->direction(StackDirection::Row)
                ->gap(Gap::ExtraSmall)
                ->schema([
                    Text::make(__('auth.forgot-password.return')),
                    Link::make(__('auth.forgot-password.login-link'))
                        ->href(route('login', absolute: false)),
                ]),
        ];
    }
}
