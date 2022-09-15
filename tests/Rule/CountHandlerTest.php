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

        $lessThanMinMessage = 'This value must contain at least {min, number} {min, plural, one{item} other{items}}.';
        $greaterThanMaxMessage = 'This value must contain at most {max, number} {max, plural, one{item} other{items}}.';

        return [
            [$rule, ...$this->createValueAndErrorsPair(1, [new Error('This value must be an array or implement \Countable interface.')])],
            [$rule, ...$this->createValueAndErrorsPair([1], [new Error($lessThanMinMessage, parameters: ['min' => 3])])],
            [$rule, ...$this->createValueAndErrorsPair([], [new Error($lessThanMinMessage, parameters: ['min' => 3])])],
            [$rule, ...$this->createValueAndErrorsPair([0, 0], [new Error($lessThanMinMessage, parameters: ['min' => 3])])],
            [$rule, ...$this->createValueAndErrorsPair([1.1], [new Error($lessThanMinMessage, parameters: ['min' => 3])])],
            [$rule, ...$this->createValueAndErrorsPair([''], [new Error($lessThanMinMessage, parameters: ['min' => 3])])],
            [$rule, ...$this->createValueAndErrorsPair(['some string'], [new Error($lessThanMinMessage, parameters: ['min' => 3])])],
            [$rule, ...$this->createValueAndErrorsPair([new stdClass()], [new Error($lessThanMinMessage, parameters: ['min' => 3])])],
            // https://www.php.net/manual/ru/class.countable.php
            [
                $rule,
                ...$this->createValueAndErrorsPair(
                    [
                        new class () {
                            protected int $myCount = 3;

                            public function count(): int
                            {
                                return $this->myCount;
                            }
                        },
                    ],
                    [new Error($lessThanMinMessage, parameters: ['min' => 3])]
                ),
            ],
            [new Count(max: 3), ...$this->createValueAndErrorsPair([0, 0, 0, 0], [new Error($greaterThanMaxMessage, parameters: ['max' => 3])])],
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
            [new Count(max: 3, greaterThanMaxMessage: 'Custom message.'), ...$this->createValueAndErrorsPair([0, 0, 0, 0], [new Error('Custom message.', parameters: ['max' => 3])])],
            [new Count(exactly: 3, notExactlyMessage: 'Custom message.'), ...$this->createValueAndErrorsPair([0, 0, 0, 0], [new Error('Custom message.', parameters: ['exactly' => 3])])],
            [new Count(min: 3, lessThanMinMessage: 'Custom message.'), ...$this->createValueAndErrorsPair([0, 0], [new Error('Custom message.', parameters: ['min' => 3])])],
        ];
    }

    protected function getRuleHandler(): RuleHandlerInterface
    {
        return new CountHandler();
    }
}
