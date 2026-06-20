<?php
declare(strict_types=1);

namespace App\Components\Settings;

use Lattice\Lattice\Core\Components\Component;

class PasskeyRegistration extends Component
{
    public static function make(): static
    {
        return new static;
    }

    protected function type(): string
    {
        return 'settings.passkey-registration';
    }
}
