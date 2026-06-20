<?php
declare(strict_types=1);

namespace App\Pages\Auth;

use App\Concerns\ResolvesFlashStatus;
use Illuminate\Http\Request;
use Lattice\Lattice\Attributes\AsPage;
use Lattice\Lattice\Core\Components\Button;
use Lattice\Lattice\Core\Components\Heading;
use Lattice\Lattice\Core\Components\Link;
use Lattice\Lattice\Core\Components\Stack;
use Lattice\Lattice\Core\Components\Text;
use Lattice\Lattice\Core\Enums\Align;
use Lattice\Lattice\Core\Enums\Gap;
use Lattice\Lattice\Core\Enums\HttpMethod;
use Lattice\Lattice\Core\Enums\PageContainer;
use Lattice\Lattice\Core\Enums\PageLayout;
use Lattice\Lattice\Core\PageSchema;
use Lattice\Lattice\Forms\Components\Form;
use Lattice\Lattice\Http\Page;

#[AsPage(layout: PageLayout::Auth, container: PageContainer::Default)]
class VerifyEmailPage extends Page
{
    use ResolvesFlashStatus;

    public function title(): string
    {
        return 'Email verification';
    }

    public function render(PageSchema $schema, Request $request): PageSchema
    {
        return $schema->schema([
            Stack::make('verify-email-heading')
                ->gap(Gap::Small)
                ->schema([
                    Heading::make('Email verification', 2),
                    Text::make('Please verify your email address by clicking on the link we just emailed to you.')
                        ->align(Align::Center),
                ]),
            Form::make('verify-email-form')
                ->action(route('verification.send', absolute: false))
                ->method(HttpMethod::Post)
                ->schema([
                    Button::make('Resend verification email')->submit(),
                    Link::make('Log out')
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

        return 'A new verification link has been sent to the email address you provided during registration.';
    }
}
