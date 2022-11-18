<?php

declare(strict_types=1);

use Yiisoft\Translator\CategorySource;
use Yiisoft\Translator\IdMessageReader;
use Yiisoft\Translator\IntlMessageFormatter;
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
        'definition' => static function (IdMessageReader $idMessageReader) use ($params): CategorySource {
            return new CategorySource(
                $params['yiisoft/validator']['translation.category'],
                $idMessageReader,
                extension_loaded('intl') ? new IntlMessageFormatter() : new SimpleMessageFormatter(),
            );
        },
        'tags' => ['translation.categorySource'],
    ],
];
