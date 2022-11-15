<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Support;

use Yiisoft\Translator\CategorySource;
use Yiisoft\Translator\IdMessageReader;
use Yiisoft\Translator\SimpleMessageFormatter;
use Yiisoft\Translator\Translator;
use Yiisoft\Translator\TranslatorInterface;

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
        $translator->addCategorySources($categorySource);

        return $translator->withDefaultCategory('validator');
    }
}
