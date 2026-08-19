<?php
declare(strict_types=1);

namespace App\Forms\Settings;

use App\Concerns\ResolvesCurrentUser;
use Illuminate\Http\Request;
use Laravel\Fortify\Actions\ConfirmTwoFactorAuthentication;
use Laravel\Fortify\Features;
use Lattice\Facades\Effects;
use Lattice\Form\Attributes\AsForm;
use Lattice\Form\Components\Form;
use Lattice\Form\Components\OtpInput;
use Lattice\Form\FormDefinition;
use Lattice\Http\LatticeResponse;

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

        return Effects::respond()->toast(__('settings.two-factor.enabled-toast'))->back();
    }
}
