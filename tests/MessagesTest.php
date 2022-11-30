<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests;

use PHPUnit\Framework\TestCase;
use Yiisoft\Translator\CategorySource;
use Yiisoft\Translator\Message\Php\MessageSource;
use Yiisoft\Translator\SimpleMessageFormatter;
use Yiisoft\Translator\Translator;
use Yiisoft\Validator\Rule\AtLeast;
use Yiisoft\Validator\Validator;

use function dirname;

final class MessagesTest extends TestCase
{
    public function testBase(): void
    {
        $locale = 'ru';

        $categorySource = new CategorySource(
            Validator::DEFAULT_TRANSLATION_CATEGORY,
            new MessageSource(dirname(__DIR__) . '/messages'),
            new SimpleMessageFormatter(),
        );

        $translator = (new Translator($locale))->withLocale($locale);
        $translator->addCategorySources($categorySource);

        $validator = new Validator(translator: $translator);

        $result = $validator->validate(
            [],
            new AtLeast(['a', 'b'])
        );

        $this->assertSame(
            ['' => ['Модель должна содержать минимум 1 заполненный атрибут.']],
            $result->getErrorMessagesIndexedByAttribute(),
        );
    }
}
