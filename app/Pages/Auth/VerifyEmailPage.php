<?php
declare(strict_types=1);

namespace App\Pages\Auth;

use App\Concerns\ResolvesFlashStatus;
use Illuminate\Http\Request;
use Lattice\Lattice\Attributes\AsPage;
use Lattice\Lattice\Ui\Components\Button;
use Lattice\Lattice\Ui\Components\Heading;
use Lattice\Lattice\Ui\Components\Link;
use Lattice\Lattice\Ui\Components\Stack;
use Lattice\Lattice\Ui\Components\Text;
use Lattice\Lattice\Ui\Enums\Align;
use Lattice\Lattice\Ui\Enums\Gap;
use Lattice\Lattice\Core\Enums\HttpMethod;
use Lattice\Lattice\Ui\Enums\PageContainer;
use Lattice\Lattice\Ui\Enums\PageLayout;
use Lattice\Lattice\Core\PageSchema;
use Lattice\Lattice\Forms\Components\Form;
use Lattice\Lattice\Http\Page;

#[AsPage(layout: PageLayout::Auth, container: PageContainer::Default)]
class VerifyEmailPage extends Page
{
    use ResolvesFlashStatus;

    public function title(): string
    {
        return __('auth.verify-email.title');
    }

    public function render(PageSchema $schema, Request $request): PageSchema
    {
        return $schema->schema([
            Stack::make('verify-email-heading')
                ->gap(Gap::Small)
                ->schema([
                    Heading::make(__('auth.verify-email.heading'), 2),
                    Text::make(__('auth.verify-email.subtitle'))
                        ->align(Align::Center),
                ]),
            Form::make('verify-email-form')
                ->action(route('verification.send', absolute: false))
                ->method(HttpMethod::Post)
                ->schema([
                    Button::make(__('auth.verify-email.resend'))->submit(),
                    Link::make(__('common.action.log-out'))
                        ->href(route('logout', absolute: false))
                        ->method(HttpMethod::Post),
                ])
                ->withoutSubmitButton()
                ->status($this->statusMessage($request)),
        ]);
    }

    private function statusMessage(Request $request): ?string
    {
        if ($this->flashStatus($request) !== 'verification-link-sent') {
            return null;
        }

        return __('auth.verify-email.sent');
    }
}
