<?php

declare(strict_types=1);

use Yiisoft\Validator\RuleHandlerContainer;
use Yiisoft\Validator\RuleHandlerResolverInterface;
use Yiisoft\Validator\Validator;
use Yiisoft\Validator\ValidatorInterface;

/* @var array $params */

return [
    ValidatorInterface::class => Validator::class,
    RuleHandlerResolverInterface::class => RuleHandlerContainer::class,
];
