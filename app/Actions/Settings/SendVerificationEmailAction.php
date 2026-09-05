<?php
declare(strict_types=1);

namespace App\Actions\Settings;

use App\Concerns\ResolvesCurrentUser;
use Lattice\Actions\ActionDefinition;
use Lattice\Actions\ActionResult;
use Lattice\Actions\Components\Action as ActionComponent;
use Lattice\Core\Attributes\AsAction;
use Lattice\Ui\Enums\Emphasis;
use Lattice\Ui\Enums\HttpMethod;
use Lattice\Ui\Enums\Variant;

#[AsAction('settings.send-verification-email')]
class SendVerificationEmailAction extends ActionDefinition
{
    use ResolvesCurrentUser;

    public function definition(ActionComponent $action): ActionComponent
    {
        return $action
            ->label(__('settings.profile.resend-verification'))
            ->method(HttpMethod::Post)
            ->emphasis(Emphasis::Link);
    }

    public function handle(): ActionResult
    {
        $user = $this->currentUser();

        if ($user->hasVerifiedEmail()) {
            return ActionResult::success()->toast(__('settings.profile.already-verified'), Variant::Info);
        }

        $user->sendEmailVerificationNotification();

        return ActionResult::success()->toast(__('settings.profile.verification-sent'));
    }
}
