<?php

declare(strict_types=1);

use Pest\Rector\Set\PestSetList;
use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\LevelSetList;
use Rector\ValueObject\PhpVersion;
use RectorLaravel\Rector\FuncCall\AppToResolveRector;
use RectorLaravel\Rector\StaticCall\DispatchToHelperFunctionsRector;
use RectorLaravel\Set\LaravelSetList;

return RectorConfig::configure()
    ->withPaths([
        __DIR__.'/app',
        __DIR__.'/tests',
    ])
    ->withCache(__DIR__.'/.rector-cache')
    ->withPhpVersion(PhpVersion::PHP_84)
    ->withSets([
        PestSetList::CODING_STYLE,
        LevelSetList::UP_TO_PHP_84,
        LaravelSetList::LARAVEL_130,
        LaravelSetList::LARAVEL_CODE_QUALITY,
    ])
    ->withSkip([
        AppToResolveRector::class,
        // Rewrites Event::dispatch() into event(new \Fully\Qualified) — a
        // fully qualified name inline reads worse than the static call.
        DispatchToHelperFunctionsRector::class,
    ]);
