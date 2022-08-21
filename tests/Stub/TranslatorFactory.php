<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Stub;

use Yiisoft\Translator\CategorySource;
use Yiisoft\Translator\Formatter\Simple\SimpleMessageFormatter;
use Yiisoft\Translator\Translator;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Validator\IdMessageReader;

final class TranslatorFactory
{
    public function create(): TranslatorInterface
    {
        $translator = new Translator(
            'en'
        );

        $categorySource = new CategorySource(
            'validator',
            new IdMessageReader(),
            new SimpleMessageFormatter()
        );
        $translator->addCategorySource($categorySource);
        return $translator->withCategory('validator');
    }
}
