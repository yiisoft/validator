<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Yiisoft\Translator\CategorySource;
use Yiisoft\Translator\Message\Php\MessageSource;
use Yiisoft\Translator\MessageFormatterInterface;
use Yiisoft\Translator\SimpleMessageFormatter;
use Yiisoft\Translator\Translator;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\Regex;
use Yiisoft\Validator\Validator;

use function dirname;

final class MessagesTest extends TestCase
{
    public function testBase(): void
    {
        $locale = 'ru';

        $categorySource = new CategorySource(
            Validator::DEFAULT_TRANSLATION_CATEGORY,
            new MessageSource($this->getMessagesPath()),
            new SimpleMessageFormatter(),
        );

        $translator = (new Translator($locale))->withLocale($locale);
        $translator->addCategorySources($categorySource);

        $validator = new Validator(translator: $translator);

        $result = $validator->validate(
            'hello',
            new Regex('~\d+~')
        );

        $this->assertSame(
            ['' => ['Значение неверно.']],
            $result->getErrorMessagesIndexedByProperty(),
        );
    }

    public static function dataNonEmpty(): array
    {
        return [
            ['ru'],
        ];
    }

    #[DataProvider('dataNonEmpty')]
    public function testNonEmpty(string $locale): void
    {
        $file = $this->getMessagesPath() . '/' . $locale . '/' . Validator::DEFAULT_TRANSLATION_CATEGORY . '.php';
        $this->assertFileExists($file);

        $messages = require $file;
        $this->assertIsArray($messages);

        foreach ($messages as $id => $message) {
            $this->assertIsString($id);
            $this->assertNotEmpty($id);
            $this->assertIsString($message);
            $this->assertNotEmpty($message);
        }
    }

    public function testErrorWithoutPostProcessing(): void
    {
        $translator = (new Translator('ru', 'en'))->addCategorySources(
            new CategorySource(
                Validator::DEFAULT_TRANSLATION_CATEGORY,
                new MessageSource($this->getMessagesPath()),
                new SimpleMessageFormatter(),
            )
        );
        $validator = new Validator(translator: $translator);

        $result = $validator->validate(
            'hello',
            [static fn() => (new Result())->addErrorWithoutPostProcessing('Value is invalid.')],
        );

        $this->assertSame(
            ['' => ['Value is invalid.']],
            $result->getErrorMessagesIndexedByProperty(),
        );
    }

    public function testErrorWithFormatOnly(): void
    {
        $translator = (new Translator('ru', 'en'))->addCategorySources(
            new CategorySource(
                Validator::DEFAULT_TRANSLATION_CATEGORY,
                new MessageSource($this->getMessagesPath()),
                new SimpleMessageFormatter(),
            )
        );
        $messageFormatter = new class () implements MessageFormatterInterface {
            public function format(string $message, array $parameters, string $locale): string
            {
                $result = $message . '!';
                foreach ($parameters as $key => $value) {
                    $result .= $key . '-' . $value;
                }
                return $result . '!' . $locale;
            }
        };
        $validator = new Validator(
            translator: $translator,
            messageFormatter: $messageFormatter,
            messageFormatterLocale: 'ru',
        );

        $result = $validator->validate(
            'hello',
            [static fn() => (new Result())->addErrorWithFormatOnly('Value is invalid.', ['a' => 3])],
        );

        $this->assertSame(
            ['' => ['Value is invalid.!a-3!ru']],
            $result->getErrorMessagesIndexedByProperty(),
        );
    }

    private function getMessagesPath(): string
    {
        return dirname(__DIR__) . '/messages';
    }
}
