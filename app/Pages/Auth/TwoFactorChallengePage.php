<?php
declare(strict_types=1);

namespace App\Pages\Auth;

use Lattice\Core\Attributes\AsPage;
use Lattice\Core\Enums\PageLayout;
use Lattice\Form\Components\Checkbox;
use Lattice\Form\Components\Form;
use Lattice\Form\Components\OtpInput;
use Lattice\Form\Components\TextInput;
use Lattice\Http\Page;
use Lattice\Ui\Components\Heading;
use Lattice\Ui\Components\Stack;
use Lattice\Ui\Components\Text;
use Lattice\Ui\Enums\Align;
use Lattice\Ui\Enums\Gap;
use Lattice\Ui\PageSchema;

#[AsPage(layout: PageLayout::Auth)]
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
