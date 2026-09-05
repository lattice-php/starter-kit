<?php
declare(strict_types=1);

namespace App\Tables\Concerns;

use Illuminate\Database\Eloquent\Model;

/**
 * A table calls actions() once per row, so an unmemoized policy check turns
 * every table with row actions into an N+1. A definition instance lives for one
 * render, which is exactly the window in which the answer cannot change.
 */
trait AuthorizesRowActions
{
    /**
     * @var array<string, bool>
     */
    private array $allowed = [];

    private function allows(string $ability, Model $subject): bool
    {
        return $this->allowed[$ability] ??= auth()->user()?->can($ability, $subject) ?? false;
    }
}
