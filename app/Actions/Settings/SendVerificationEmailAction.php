<?php
declare(strict_types=1);

namespace App\Actions\Settings;

use App\Concerns\ResolvesCurrentUser;
use Illuminate\Http\Request;
use Lattice\Lattice\Actions\ActionDefinition;
use Lattice\Lattice\Actions\ActionResult;
use Lattice\Lattice\Actions\Components\Action as ActionComponent;
use Lattice\Lattice\Attributes\AsAction;
use Lattice\Lattice\Core\Enums\ButtonVariant;
use Lattice\Lattice\Core\Enums\HttpMethod;
use Lattice\Lattice\Core\Enums\Variant;

#[AsAction('settings.send-verification-email')]
class SendVerificationEmailAction extends ActionDefinition
{
    use ResolvesCurrentUser;

    public function definition(ActionComponent $action): ActionComponent
    {
        return $action
            ->label(__('settings.profile.resend-verification'))
            ->method(HttpMethod::Post)
            ->variant(ButtonVariant::Link);
    }

    public function handle(Request $request): ActionResult
    {
        $user = $this->currentUser();

        if ($user->hasVerifiedEmail()) {
            return ActionResult::success()->toast(Variant::Info, __('settings.profile.already-verified'));
        }

        $user->sendEmailVerificationNotification();

        return ActionResult::success()->toast(Variant::Success, __('settings.profile.verification-sent'));
    }
}
