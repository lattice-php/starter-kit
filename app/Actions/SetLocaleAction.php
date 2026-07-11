<?php
declare(strict_types=1);

namespace App\Actions;

use App\Models\User;
use Illuminate\Http\Request;
use Lattice\Lattice\Actions\ActionDefinition;
use Lattice\Lattice\Actions\ActionResult;
use Lattice\Lattice\Actions\Components\Action;
use Lattice\Lattice\Attributes\AsAction;
use Lattice\Lattice\Ui\Enums\ButtonVariant;

#[AsAction('app.locale.set')]
class SetLocaleAction extends ActionDefinition
{
    public function definition(Action $action): Action
    {
        return $action->variant(ButtonVariant::Ghost);
    }

    public function handle(Request $request): ActionResult
    {
        $locale = $this->context('locale');
        $locales = config('lattice.i18n.locales', []);

        if (! is_string($locale) || ! is_array($locales) || ! in_array($locale, $locales, true)) {
            return ActionResult::failure();
        }

        $user = $request->user();

        if ($user instanceof User) {
            $user->update(['locale' => $locale]);
        }

        return ActionResult::success()->localeChange($locale);
    }
}
