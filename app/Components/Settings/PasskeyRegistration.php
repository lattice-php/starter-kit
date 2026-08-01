<?php
declare(strict_types=1);

namespace App\Components\Settings;

use Lattice\Lattice\Attributes\AsComponent;
use Lattice\Lattice\Ui\Components\Component;

#[AsComponent('settings.passkey-registration')]
class PasskeyRegistration extends Component
{
    public static function make(): static
    {
        return new static;
    }
}
