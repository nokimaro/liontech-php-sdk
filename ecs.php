<?php

declare(strict_types=1);

use Symplify\EasyCodingStandard\Config\ECSConfig;

return ECSConfig::configure()
    ->withPreparedSets(
        psr12: true,
        common: true,
        symplify: true,
    )
    ->withPaths([
        __DIR__ . '/src',
        __DIR__ . '/tests',
        __DIR__ . '/examples',
    ])
    ->withRootFiles()
    ->withSkip([
        // Add files or rules to skip
    ]);
