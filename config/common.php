<?php

declare(strict_types=1);

use Psr\Container\ContainerInterface;
use Yiisoft\Validator\Formatter;
use Yiisoft\Validator\FormatterInterface;
use Yiisoft\Validator\RuleValidatorStorage;
use Yiisoft\Validator\Validator;
use Yiisoft\Validator\ValidatorInterface;

/* @var array $params */

return [
    ValidatorInterface::class => Validator::class,
    RuleValidatorStorage::class => function (ContainerInterface $container) use ($params) {
        $validators = [];

        $validatorsClasses = $params['yiisoft/validator']['validators'];
        foreach ($validatorsClasses as $class) {
            $validators[$class::getRuleClassName()] = $container->get($class);
        }

        return new RuleValidatorStorage($validators);
    },
    FormatterInterface::class => Formatter::class,
];
