<?php
declare(strict_types=1);

namespace App\Pages\Auth;

use App\Concerns\ResolvesFlashStatus;
use Illuminate\Http\Request;
use Lattice\Core\Attributes\AsPage;
use Lattice\Core\Enums\PageLayout;
use Lattice\Form\Components\Form;
use Lattice\Http\Page;
use Lattice\Ui\Components\Button;
use Lattice\Ui\Components\Heading;
use Lattice\Ui\Components\Link;
use Lattice\Ui\Components\Stack;
use Lattice\Ui\Components\Text;
use Lattice\Ui\Enums\Gap;
use Lattice\Ui\Enums\HttpMethod;
use Lattice\Ui\Enums\TextAlign;
use Lattice\Ui\PageSchema;

#[AsPage(layout: PageLayout::Auth)]
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
                        ->align(TextAlign::Center),
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
