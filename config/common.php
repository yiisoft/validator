<?php

declare(strict_types=1);

use Psr\Container\ContainerInterface;
use Yiisoft\Translator\CategorySource;
use Yiisoft\Translator\MessageFormatterInterface;
use Yiisoft\Translator\SimpleMessageFormatter;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Validator\IdMessageReader;
use Yiisoft\Validator\RuleHandlerResolverInterface;
use Yiisoft\Validator\SimpleRuleHandlerContainer;
use Yiisoft\Validator\TranslateValidatorDecorator;
use Yiisoft\Validator\Validator;
use Yiisoft\Validator\ValidatorInterface;

/* @var array $params */

return [
    ValidatorInterface::class => function (
        ContainerInterface $container,
        RuleHandlerResolverInterface $ruleHandlerResolver
    ) {
        $validator = new Validator($ruleHandlerResolver);

        if (!$container->has(TranslatorInterface::class)) {
            return $validator;
        }

        return new TranslateValidatorDecorator(
            new Validator($ruleHandlerResolver),
            $container->get(TranslatorInterface::class),
        );
    },
    RuleHandlerResolverInterface::class => SimpleRuleHandlerContainer::class,
    'validator.categorySource' => static function (ContainerInterface $container) use ($params) {
        $messageSource = $container->get('validator.messageSource');
        $messageFormatter = $container->has(MessageFormatterInterface::class)
            ? $container->get(MessageFormatterInterface::class)
            : new SimpleMessageFormatter();

        return new CategorySource(
            $params['yiisoft/translator']['validatorCategory'],
            $messageSource,
            $messageFormatter,
        );
    },
    'validator.messageSource' => IdMessageReader::class,
];
