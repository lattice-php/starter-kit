<?php
declare(strict_types=1);

namespace App\Actions\Settings;

use Laravel\Passkeys\Passkey;
use Lattice\Actions\ActionDefinition;
use Lattice\Actions\ActionResult;
use Lattice\Actions\Components\Action;
use Lattice\Core\Attributes\AsAction;
use Lattice\Ui\Enums\Variant;

#[AsAction('settings.passkeys.delete')]
class DeletePasskey extends ActionDefinition
{
    public function definition(Action $action): Action
    {
        return $action
            ->label(__('settings.passkeys.remove'))
            ->variant(Variant::Danger)
            ->confirm(
                title: __('settings.passkeys.remove-confirm-title'),
                description: __('settings.passkeys.remove-confirm-description'),
                confirmLabel: __('settings.passkeys.remove'),
            );
    }

    /**
     * The `passkey` context resolver scopes the lookup to the signed-in user,
     * so a reference naming someone else's passkey resolves to nothing.
     */
    public function handle(): ActionResult
    {
        /** @var Passkey $passkey */
        $passkey = $this->contextModel('passkey');

        $passkey->delete();

        return ActionResult::success()
            ->toast(__('settings.passkeys.removed'))
            ->reloadComponent('settings.passkeys');
    }
}
