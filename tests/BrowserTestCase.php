<?php

declare(strict_types=1);

namespace Tests;

/**
 * The one suite that must resolve the real Vite manifest: a browser test drives
 * the built bundle, so it needs `npm run build` rather than the stub.
 */
abstract class BrowserTestCase extends TestCase
{
    protected bool $stubsVite = false;
}
