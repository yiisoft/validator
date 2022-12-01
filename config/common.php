<?php

declare(strict_types=1);

use Yiisoft\Translator\CategorySource;
use Yiisoft\Translator\IdMessageReader;
use Yiisoft\Translator\IntlMessageFormatter;
use Yiisoft\Translator\Message\Php\MessageSource;
use Yiisoft\Translator\SimpleMessageFormatter;
use Yiisoft\Validator\RuleHandlerResolverInterface;
use Yiisoft\Validator\SimpleRuleHandlerContainer;
use Yiisoft\Validator\Validator;
use Yiisoft\Validator\ValidatorInterface;

/* @var array $params */

return [
    ValidatorInterface::class => [
        'class' => Validator::class,
        '__construct()' => [
            'translationCategory' => $params['yiisoft/validator']['translation.category'],
        ],
    ],
    RuleHandlerResolverInterface::class => SimpleRuleHandlerContainer::class,
    'yii.validator.categorySource' => [
        'definition' => static function () use ($params): CategorySource {
            $reader = class_exists(MessageSource::class)
                ? new MessageSource(dirname(__DIR__) . '/messages')
                : new IdMessageReader(); // @codeCoverageIgnore

            $formatter = extension_loaded('intl')
                ? new IntlMessageFormatter()
                : new SimpleMessageFormatter(); // @codeCoverageIgnore

            return new CategorySource($params['yiisoft/validator']['translation.category'], $reader, $formatter);
        },
        'tags' => ['translation.categorySource'],
    ],
];
