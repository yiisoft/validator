<?php

declare(strict_types=1);

use Yiisoft\Validator\Formatter;
use Yiisoft\Validator\FormatterInterface;
use Yiisoft\Validator\RuleHandlerResolverInterface;
use Yiisoft\Validator\StaticRuleHandlerResolver;
use Yiisoft\Validator\Validator;
use Yiisoft\Validator\ValidatorInterface;

/* @var array $params */

return [
    ValidatorInterface::class => Validator::class,
    FormatterInterface::class => Formatter::class,
    RuleHandlerResolverInterface::class => [
        'class' => StaticRuleHandlerResolver::class,
        '__construct()' => [
            'handlers' => $params['ruleHandlers'],
        ],
    ]
];
