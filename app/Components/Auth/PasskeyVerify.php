<?php
declare(strict_types=1);

namespace App\Components\Auth;

use Lattice\Core\Attributes\AsComponent;
use Lattice\Ui\Components\Component;

#[AsComponent('auth.passkey-verify')]
class PasskeyVerify extends Component
{
    public string $optionsUrl;

    public string $submitUrl;

    public ?string $label = null;

    public ?string $loadingLabel = null;

    public ?string $separator = null;

    public static function make(string $optionsUrl, string $submitUrl): static
    {
        $component = new static;
        $component->optionsUrl = $optionsUrl;
        $component->submitUrl = $submitUrl;

        return $component;
    }

    public function label(string $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function loadingLabel(string $loadingLabel): static
    {
        $this->loadingLabel = $loadingLabel;

        return $this;
    }

    public function separator(string $separator): static
    {
        $this->separator = $separator;

        return $this;
    }
}
