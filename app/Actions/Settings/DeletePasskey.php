<?php
declare(strict_types=1);

namespace App\Actions\Settings;

use App\Concerns\ResolvesCurrentUser;
use Illuminate\Http\Request;
use Lattice\Lattice\Actions\ActionDefinition;
use Lattice\Lattice\Actions\ActionResult;
use Lattice\Lattice\Actions\Components\Action;
use Lattice\Lattice\Attributes\AsAction;
use Lattice\Lattice\Core\Enums\ButtonVariant;
use Lattice\Lattice\Core\Enums\Variant;

#[AsAction('settings.passkeys.delete')]
class DeletePasskey extends ActionDefinition
{
    use ResolvesCurrentUser;

    public function definition(Action $action): Action
    {
        return $action
            ->label('Remove passkey')
            ->variant(ButtonVariant::Destructive)
            ->confirm(
                title: 'Remove passkey?',
                description: 'You will no longer be able to use this passkey to sign in.',
                confirmLabel: 'Remove passkey',
            );
    }

    #[\Override]
    public function authorize(Request $request): bool
    {
        return $this->currentUser()->passkeys()->whereKey($this->context('passkey'))->exists();
    }

    public function handle(Request $request): ActionResult
    {
        $this->currentUser()->passkeys()->whereKey($this->context('passkey'))->delete();

        return ActionResult::success()
            ->toast(Variant::Success, __('Passkey removed.'))
            ->reloadComponent('settings.passkeys');
    }
}
