<?php
declare(strict_types=1);

namespace App\Actions;

use App\Models\User;
use Illuminate\Http\Request;
use Lattice\Actions\ActionDefinition;
use Lattice\Actions\ActionResult;
use Lattice\Actions\Components\Action;
use Lattice\Core\Attributes\AsAction;
use Lattice\Ui\Enums\Emphasis;

#[AsAction('app.locale.set')]
class SetLocaleAction extends ActionDefinition
{
    public function definition(Action $action): Action
    {
        return $action->emphasis(Emphasis::Ghost);
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
