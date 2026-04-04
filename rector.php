<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Set\ValueObject\SetList;

return RectorConfig::configure()
    ->withPaths([__DIR__ . '/src', __DIR__ . '/tests', __DIR__ . '/examples'])
    ->withSets([LevelSetList::UP_TO_PHP_83, SetList::TYPE_DECLARATION, SetList::CODE_QUALITY, SetList::DEAD_CODE])
    ->withSkip([
        // Keep PHPStan var annotations for type safety
        \Rector\DeadCode\Rector\Node\RemoveNonExistingVarAnnotationRector::class => [__DIR__ . '/src/Json.php'],
        // Skip ApiClient - Rector corrupts it
        __DIR__ . '/src/Http/ApiClient.php',
    ]);
