<?php

declare(strict_types=1);

use Psr\Container\ContainerInterface;
use Yiisoft\Translator\CategorySource;
use Yiisoft\Translator\SimpleMessageFormatter;
use Yiisoft\Validator\IdMessageReader;
use Yiisoft\Validator\RuleHandlerResolverInterface;
use Yiisoft\Validator\SimpleRuleHandlerContainer;
use Yiisoft\Validator\Validator;
use Yiisoft\Validator\ValidatorInterface;

/* @var array $params */

return [
    ValidatorInterface::class => Validator::class,
    RuleHandlerResolverInterface::class => SimpleRuleHandlerContainer::class,
    'validator.categorySource' => [
        'definition' => static function (ContainerInterface $container) use ($params) {
            $messageSource = $container->get('validator.messageSource');
            $messageFormatter = new SimpleMessageFormatter();

            return new CategorySource(
                $params['yiisoft/translator']['validatorCategory'],
                $messageSource,
                $messageFormatter,
            );
        },
        'tags' => ['translation.categorySource'],
    ],
    'validator.messageSource' => IdMessageReader::class,
];
