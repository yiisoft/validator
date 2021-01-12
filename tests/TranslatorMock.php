<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests;

use Yiisoft\Translator\Category;
use Yiisoft\Translator\MessageFormatterInterface;
use Yiisoft\Translator\MessageReaderInterface;
use Yiisoft\Translator\Translator;
use Yiisoft\Translator\TranslatorInterface;
use PHPUnit\Framework\TestCase;

abstract class TranslatorMock extends TestCase
{
    protected function createTranslatorMock(?array $returnMessage = null): TranslatorInterface
    {
        return new Translator(
            new Category(
                'app',
                $this->createMessageReader($returnMessage??[]),
                $this->createMessageFormatter()
            ),
            'en'
        );
    }

    private function createMessageFormatter(): MessageFormatterInterface
    {
        return new class() implements MessageFormatterInterface {
            public function format(string $message, array $parameters, string $locale): string
            {
                $replacements = [];
                foreach ($parameters as $key => $value) {
                    if (is_scalar($value)) {
                        $replacements['{' . $key . '}'] = $value;
                    }
                }
                return strtr($message, $replacements);
            }
        };
    }

    private function createMessageReader(array $messages): MessageReaderInterface
    {
        return new class($messages) implements MessageReaderInterface {
            private array $messages;

            public function __construct(array $messages)
            {
                $this->messages = $messages;
            }

            public function getMessage(string $id, string $category, string $locale, array $parameters = []): ?string
            {
                return $this->messages[$id] ?? null;
            }
        };
    }
}
