<?php
declare(strict_types=1);

namespace App\Pages\Auth;

use Lattice\Lattice\Attributes\AsPage;
use Lattice\Lattice\Ui\Components\Heading;
use Lattice\Lattice\Ui\Components\Stack;
use Lattice\Lattice\Ui\Components\Text;
use Lattice\Lattice\Ui\Enums\Align;
use Lattice\Lattice\Ui\Enums\Gap;
use Lattice\Lattice\Ui\Enums\PageContainer;
use Lattice\Lattice\Ui\Enums\PageLayout;
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
        return __('auth.two-factor.title');
    }

    public function render(PageSchema $schema): PageSchema
    {
        return $schema->schema([
            Stack::make('two-factor-challenge-heading')
                ->gap(Gap::Small)
                ->schema([
                    Heading::make(__('auth.two-factor.heading'), 2),
                    Text::make(__('auth.two-factor.subtitle'))
                        ->align(Align::Center),
                ]),
            Form::make('two-factor-challenge')
                ->action(route('two-factor.login.store', absolute: false))
                ->submitLabel(__('auth.two-factor.continue'))
                ->schema([
                    OtpInput::make('code', __('auth.two-factor.code'))
                        ->length(6)
                        ->visibleWhen('use_recovery_code', false),
                    TextInput::make('recovery_code', __('auth.two-factor.recovery-code'))
                        ->helperText(__('auth.two-factor.recovery-help'))
                        ->visibleWhen('use_recovery_code', true),
                    Checkbox::make('use_recovery_code', __('auth.two-factor.use-recovery')),
                ]),
        ]);
    }
}
