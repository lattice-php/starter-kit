<?php
declare(strict_types=1);

namespace App\Components\Settings;

use Lattice\Core\Attributes\AsComponent;
use Lattice\Ui\Components\Component;

#[AsComponent('settings.passkey-registration')]
class PasskeyRegistration extends Component
{
    public static function make(): static
    {
        return new static;
    }
}
