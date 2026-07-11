<?php
declare(strict_types=1);

namespace App\Forms\Settings;

use App\Concerns\ResolvesCurrentUser;
use Illuminate\Http\Request;
use Laravel\Fortify\Actions\ConfirmTwoFactorAuthentication;
use Laravel\Fortify\Features;
use Lattice\Lattice\Attributes\AsForm;
use Lattice\Lattice\Ui\Enums\Variant;
use Lattice\Lattice\Forms\Components\Form;
use Lattice\Lattice\Forms\Components\OtpInput;
use Lattice\Lattice\Forms\FormDefinition;
use Lattice\Lattice\Http\LatticeResponse;

#[AsForm('settings.two-factor.confirm')]
class ConfirmTwoFactorForm extends FormDefinition
{
    use ResolvesCurrentUser;

    public function __construct(private readonly ConfirmTwoFactorAuthentication $confirm) {}

    public function definition(Form $form, Request $request): Form
    {
        return $form
            ->submitLabel(__('settings.two-factor.confirm'))
            ->schema([
                OtpInput::make('code', __('settings.two-factor.code'))
                    ->length(6)
                    ->helperText(__('settings.two-factor.code-help'))
                    ->rules(['required', 'string']),
            ]);
    }

    public function handle(Request $request): LatticeResponse
    {
        $user = $this->currentUser();

        abort_unless(Features::canManageTwoFactorAuthentication(), 403);

        ($this->confirm)($user, (string) $request->input('code'));

        return $this->toast(Variant::Success, __('settings.two-factor.enabled-toast'))->back();
    }
}
