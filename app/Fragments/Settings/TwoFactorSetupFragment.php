<?php
declare(strict_types=1);

namespace App\Fragments\Settings;

use App\Forms\Settings\ConfirmTwoFactorForm;
use App\Http\Requests\Settings\TwoFactorAuthenticationRequest;
use App\Models\User;
use Laravel\Fortify\Features;
use Laravel\Fortify\Fortify;
use Lattice\Lattice\Attributes\AsFragment;
use Lattice\Lattice\Core\PageSchema;
use Lattice\Lattice\Forms\Components\Form;
use Lattice\Lattice\Fragments\FragmentDefinition;
use Lattice\Lattice\Ui\Components\RawBlock;
use Lattice\Lattice\Ui\Components\Stack;
use Lattice\Lattice\Ui\Components\Text;
use Lattice\Lattice\Ui\Enums\Align;
use Lattice\Lattice\Ui\Enums\Gap;

#[AsFragment('settings.two-factor-setup')]
class TwoFactorSetupFragment extends FragmentDefinition
{
    // Resolving the typed request reconciles the user's two-factor state via its passedValidation().
    public function __construct(private readonly TwoFactorAuthenticationRequest $request) {}

    public function schema(PageSchema $schema): PageSchema
    {
        abort_unless(Features::canManageTwoFactorAuthentication(), 403);

        $user = $this->request->user();

        abort_unless($user instanceof User, 403);

        if ($user->hasEnabledTwoFactorAuthentication() || $user->two_factor_secret === null) {
            return $schema->schema([
                Text::make(__('settings.two-factor.already-enabled')),
            ]);
        }

        return $schema->schema([
            Stack::make('two-factor-setup')
                ->align(Align::Center)
                ->gap(Gap::Medium)
                ->schema([
                    RawBlock::make('two-factor-qr-code')->html($user->twoFactorQrCodeSvg()),
                    Text::make(__('settings.two-factor.setup-key')),
                    Text::make(Fortify::currentEncrypter()->decrypt($user->two_factor_secret)),
                    Form::use(ConfirmTwoFactorForm::class),
                ]),
        ]);
    }
}
