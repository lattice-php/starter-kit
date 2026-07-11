<?php
declare(strict_types=1);

namespace App\Actions\Settings;

use App\Concerns\ResolvesCurrentUser;
use Illuminate\Http\Request;
use Lattice\Lattice\Actions\ActionDefinition;
use Lattice\Lattice\Actions\ActionResult;
use Lattice\Lattice\Actions\Components\Action;
use Lattice\Lattice\Attributes\AsAction;
use Lattice\Lattice\Ui\Enums\ButtonVariant;
use Lattice\Lattice\Ui\Enums\Variant;

#[AsAction('settings.passkeys.delete')]
class DeletePasskey extends ActionDefinition
{
    use ResolvesCurrentUser;

    public function definition(Action $action): Action
    {
        return $action
            ->label(__('settings.passkeys.remove'))
            ->variant(ButtonVariant::Destructive)
            ->confirm(
                title: __('settings.passkeys.remove-confirm-title'),
                description: __('settings.passkeys.remove-confirm-description'),
                confirmLabel: __('settings.passkeys.remove'),
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
            ->toast(Variant::Success, __('settings.passkeys.removed'))
            ->reloadComponent('settings.passkeys');
    }
}
