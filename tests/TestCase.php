<?php

declare(strict_types=1);

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Laravel\Fortify\Features;
use Lattice\Support\Testing\InteractsWithLatticeComponents;

abstract class TestCase extends BaseTestCase
{
    use InteractsWithLatticeComponents;

    /**
     * Rendering a real route resolves @vite, which throws without
     * public/build/manifest.json. Feature tests assert Inertia props and the
     * Lattice component tree, never the Blade shell's asset tags, so they run
     * against a stub and need no build — that is what keeps them out of the
     * node toolchain in CI. Only BrowserTestCase turns this off.
     */
    protected bool $stubsVite = true;

    protected function setUp(): void
    {
        parent::setUp();

        if ($this->stubsVite) {
            $this->withoutVite();
        }
    }

    protected function skipUnlessFortifyHas(string $feature, ?string $message = null): void
    {
        if (! Features::enabled($feature)) {
            $this->markTestSkipped($message ?? "Fortify feature [{$feature}] is not enabled.");
        }
    }
}
