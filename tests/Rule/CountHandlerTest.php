<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use Countable;
use stdClass;
use Yiisoft\Validator\Error;
use Yiisoft\Validator\Rule\Count;
use Yiisoft\Validator\Rule\CountHandler;
use Yiisoft\Validator\RuleHandlerInterface;

final class CountHandlerTest extends AbstractRuleValidatorTest
{
    public function failedValidationProvider(): array
    {
        $rule = new Count(min: 3);
        $message = 'This value must contain at least {min, number} {min, plural, one{item} other{items}}.';
        $parameters = ['min' => 3];

        return [
            [
                $rule,
                [1],
                [new Error($this->formatMessage($message, $parameters))],
            ],
            [
                $rule,
                [],
                [new Error($this->formatMessage($message, $parameters))],
            ],
            [
                $rule,
                [0, 0],
                [new Error($this->formatMessage($message, $parameters))],
            ],
            [
                $rule,
                [1.1],
                [new Error($this->formatMessage($message, $parameters))],
            ],
            [
                $rule,
                [''],
                [new Error($this->formatMessage($message, $parameters))],
            ],
            [
                $rule,
                ['some string'],
                [new Error($this->formatMessage($message, $parameters))],
            ],
            [
                $rule,
                [new stdClass()],
                [new Error($this->formatMessage($message, $parameters))],
            ],
            // https://www.php.net/manual/ru/class.countable.php
            [
                $rule,
                [
                    new class () {
                        protected int $myCount = 3;

                        public function count(): int
                        {
                            return $this->myCount;
                        }
                    },
                ],
                [new Error($this->formatMessage($message, $parameters))],
            ],
            [
                new Count(max: 3),
                [0, 0, 0, 0],
                [new Error($this->formatMessage('This value must contain at most {max, number} {max, plural, one{item} other{items}}.', ['max' => 3]))],
            ],
        ];
    }

    public function passedValidationProvider(): array
    {
        return [
            [new Count(min: 3), [0, 0, 0]],
            [new Count(min: 3), [0, 0, 0, 0]],
            [new Count(exactly: 3), [0, 0, 0]],
            [new Count(max: 3), []],
            [new Count(max: 3), [0, 0]],
            [new Count(max: 3), [0, 0, 0]],
            [
                new Count(min: 3),
                new class () implements Countable {
                    protected int $myCount = 3;

                    public function count(): int
                    {
                        return $this->myCount;
                    }
                },
            ],
        ];
    }

    public function customErrorMessagesProvider(): array
    {
        return [
            [
                new Count(max: 3, tooManyItemsMessage: 'Custom message.'),
                [0, 0, 0, 0],
                [new Error('Custom message.')],
            ],
            [
                new Count(exactly: 3, notExactlyMessage: 'Custom message.'),
                [0, 0, 0, 0],
                [new Error('Custom message.')],
            ],
            [
                new Count(min: 3, tooFewItemsMessage: 'Custom message.'),
                [0, 0],
                [new Error('Custom message.')],
            ],
        ];
    }

    protected function getRuleHandler(): RuleHandlerInterface
    {
        return new CountHandler($this->getTranslator());
    }
}
