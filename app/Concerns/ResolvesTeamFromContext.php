<?php

declare(strict_types=1);

namespace App\Concerns;

use App\Models\Team;
use Lattice\Lattice\Core\Concerns\ResolvesContextModels;

trait ResolvesTeamFromContext
{
    use ResolvesContextModels;

    protected function teamFromContext(): Team
    {
        return $this->contextModel('team', Team::class);
    }

    protected function optionalTeamFromContext(): ?Team
    {
        return $this->contextModelOrNull('team', Team::class);
    }
}
