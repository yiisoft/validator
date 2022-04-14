<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule\Count;

use Countable;
use stdClass;
use Yiisoft\Validator\Error;
use Yiisoft\Validator\Rule\Count\Count;
use Yiisoft\Validator\Rule\Count\CountValidator;
use Yiisoft\Validator\Rule\RuleValidatorInterface;
use Yiisoft\Validator\Tests\Rule\AbstractRuleValidatorTest;

final class CountValidatorTest extends AbstractRuleValidatorTest
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
                [new Error($message, $parameters)],
            ],
            [
                $rule,
                [],
                [new Error($message, $parameters)],
            ],
            [
                $rule,
                [0, 0],
                [new Error($message, $parameters)],
            ],
            [
                $rule,
                [1.1],
                [new Error($message, $parameters)],
            ],
            [
                $rule,
                [''],
                [new Error($message, $parameters)],
            ],
            [
                $rule,
                ['some string'],
                [new Error($message, $parameters)],
            ],
            [
                $rule,
                [new stdClass()],
                [new Error($message, $parameters)],
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
                [new Error($message, $parameters)],
            ],
            [
                new Count(max: 3),
                [0, 0, 0, 0],
                [new Error('This value must contain at most {max, number} {max, plural, one{item} other{items}}.', ['max' => 3])],
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
                [new Error('Custom message.', ['max' => 3])],
            ],
            [
                new Count(exactly: 3, notExactlyMessage: 'Custom message.'),
                [0, 0, 0, 0],
                [new Error('Custom message.', ['exactly' => 3])],
            ],
            [
                new Count(min: 3, tooFewItemsMessage: 'Custom message.'),
                [0, 0],
                [new Error('Custom message.', ['min' => 3])],
            ],
        ];
    }

    protected function getValidator(): RuleValidatorInterface
    {
        return new CountValidator();
    }

    protected function getConfigClassName(): string
    {
        return Count::class;
    }
}
