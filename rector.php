<?php

declare(strict_types=1);

use Rector\CodeQuality\Rector\Class_\InlineConstructorDefaultToPropertyRector;
use Rector\Config\RectorConfig;
use Rector\TypeDeclaration\Rector\Property\TypedPropertyFromStrictConstructorRector;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/config',
        __DIR__ . '/src',
        __DIR__ . '/tests',
        __DIR__ . '/workbench',
    ])
   // uncomment to reach your current PHP version
    ->withPhpSets(php82: true)
    ->withTypeCoverageLevel(0)
    ->withDeadCodeLevel(0)
    ->withCodeQualityLevel(0)
    ->withCodingStyleLevel(0)
   // register single rule
    ->withRules([
        TypedPropertyFromStrictConstructorRector::class,
        InlineConstructorDefaultToPropertyRector::class,
    ])
    ->withPreparedSets(
        naming: true,
        privatization: true,
        earlyReturn: true,
        rectorPreset: true,
    );
