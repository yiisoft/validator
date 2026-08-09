<?php

declare(strict_types=1);

use ShipMonk\ComposerDependencyAnalyser\Config\Configuration;
use ShipMonk\ComposerDependencyAnalyser\Config\ErrorType;

return (new Configuration())
    ->disableComposerAutoloadPathScan()
    ->setFileExtensions(['php'])
    ->addPathToScan(__DIR__ . '/config', isDev: false)
    ->addPathToScan(__DIR__ . '/src', isDev: false)
    ->addPathToScan(__DIR__ . '/tests', isDev: true)
    // Optional PHP extensions used by specific rules only, declared in "suggest".
    ->ignoreErrorsOnExtensions(['ext-fileinfo', 'ext-intl'], [ErrorType::SHADOW_DEPENDENCY])
    // Optional integrations used conditionally (class_exists()/attributes), intentionally kept as dev dependencies.
    ->ignoreErrorsOnPackages(
        ['jetbrains/phpstorm-attributes', 'yiisoft/translator-message-php', 'yiisoft/yii-debug'],
        [ErrorType::DEV_DEPENDENCY_IN_PROD],
    );
