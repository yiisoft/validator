<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Support;

use Yiisoft\Translator\CategorySource;
use Yiisoft\Translator\SimpleMessageFormatter;
use Yiisoft\Translator\Translator;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Validator\IdMessageReader;

final class TranslatorFactory
{
    public function create(): TranslatorInterface
    {
        $translator = new Translator('en');
        $categorySource = new CategorySource(
            'validator',
            new IdMessageReader(),
            new SimpleMessageFormatter()
        );
        $translator->addCategorySource($categorySource);

        return $translator->withCategory('validator');
    }
}
