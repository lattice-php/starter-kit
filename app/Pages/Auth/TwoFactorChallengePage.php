<?php
declare(strict_types=1);

namespace App\Pages\Auth;

use Lattice\Lattice\Attributes\AsPage;
use Lattice\Lattice\Core\Components\Heading;
use Lattice\Lattice\Core\Components\Stack;
use Lattice\Lattice\Core\Components\Text;
use Lattice\Lattice\Core\Enums\Align;
use Lattice\Lattice\Core\Enums\Gap;
use Lattice\Lattice\Core\Enums\PageContainer;
use Lattice\Lattice\Core\Enums\PageLayout;
use Lattice\Lattice\Core\PageSchema;
use Lattice\Lattice\Forms\Components\Checkbox;
use Lattice\Lattice\Forms\Components\Form;
use Lattice\Lattice\Forms\Components\OtpInput;
use Lattice\Lattice\Forms\Components\TextInput;
use Lattice\Lattice\Http\Page;

#[AsPage(layout: PageLayout::Auth, container: PageContainer::Default)]
class TwoFactorChallengePage extends Page
{
    public function title(): string
    {
        return 'Two-factor authentication';
    }

    public function render(PageSchema $schema): PageSchema
    {
        return $schema->schema([
            Stack::make('two-factor-challenge-heading')
                ->gap(Gap::Small)
                ->schema([
                    Heading::make('Two-factor authentication', 2),
                    Text::make('Enter the code from your authenticator app to continue')
                        ->align(Align::Center),
                ]),
            Form::make('two-factor-challenge')
                ->action(route('two-factor.login.store', absolute: false))
                ->submitLabel('Continue')
                ->schema([
                    OtpInput::make('code', 'Authentication code')
                        ->length(6)
                        ->visibleWhen('use_recovery_code', false),
                    TextInput::make('recovery_code', 'Recovery code')
                        ->helperText('Confirm access by entering one of your emergency recovery codes.')
                        ->visibleWhen('use_recovery_code', true),
                    Checkbox::make('use_recovery_code', 'Use a recovery code instead'),
                ]),
        ]);
    }
}
