<?php

namespace Yiisoft\Validator\Tests\Stub;

use Yiisoft\Translator\CategorySource;
use Yiisoft\Translator\Formatter\Simple\SimpleMessageFormatter;
use Yiisoft\Translator\MessageReaderInterface;
use Yiisoft\Translator\Translator;
use Yiisoft\Translator\TranslatorInterface;

final class TranslatorFactory
{
    public function create(): TranslatorInterface
    {
        $translator = new Translator(
            'en'
        );

        $categorySource = new CategorySource(
            'validator',
            new class () implements MessageReaderInterface {
                public function getMessage(
                    string $id,
                    string $category,
                    string $locale,
                    array $parameters = []
                ): ?string {
                    return $id;
                }

                public function getMessages(string $category, string $locale): array
                {
                    return [];
                }
            },
            new SimpleMessageFormatter()
        );
        $translator->addCategorySource($categorySource);
        return $translator->withCategory('validator');
    }
}
