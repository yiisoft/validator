<?php

declare(strict_types=1);

use Yiisoft\Translator\Formatter\Simple\SimpleMessageFormatter;
use Yiisoft\Translator\MessageFormatterInterface;
use Yiisoft\Validator\RuleHandlerResolverInterface;
use Yiisoft\Validator\SimpleRuleHandlerContainer;
use Yiisoft\Validator\Validator;
use Yiisoft\Validator\ValidatorInterface;

/* @var array $params */

return [
    ValidatorInterface::class => Validator::class,
    RuleHandlerResolverInterface::class => SimpleRuleHandlerContainer::class,
    MessageFormatterInterface::class => SimpleMessageFormatter::class,
];
