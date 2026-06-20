<?php
declare(strict_types=1);

namespace App\Concerns;

use App\Models\Team;

trait ResolvesTeamFromContext
{
    protected function teamFromContext(): Team
    {
        return Team::where('slug', $this->contextString('team'))->firstOrFail();
    }

    protected function optionalTeamFromContext(): ?Team
    {
        $slug = $this->context('team');

        if (! is_string($slug) || $slug === '') {
            return null;
        }

        return Team::where('slug', $slug)->first();
    }

    protected function contextString(string $key): string
    {
        $value = $this->context($key);

        abort_unless(is_string($value) && $value !== '', 404);

        return $value;
    }

    protected function contextInt(string $key): int
    {
        $value = $this->context($key);

        abort_unless(is_numeric($value), 404);

        return (int) $value;
    }
}
