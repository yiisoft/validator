<?php

declare(strict_types=1);

use Psr\Container\ContainerInterface;
use Yiisoft\Translator\CategorySource;
use Yiisoft\Translator\MessageFormatterInterface;
use Yiisoft\Translator\MessageReaderInterface;
use Yiisoft\Translator\TranslatorInterface;

return [
    'validator' => static function (ContainerInterface $container) {
        /** @var TranslatorInterface $translator */
        $translator = $container->get(TranslatorInterface::class);
        $formatter = $container->get(MessageFormatterInterface::class);
        $messageSource = $container->get(MessageReaderInterface::class);

        $category = new CategorySource(
            'validator',
            $messageSource,
            $formatter
        );
        $translator->addCategorySource($category);
    },
];
