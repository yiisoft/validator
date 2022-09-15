<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use stdClass;
use Yiisoft\Validator\Error;
use Yiisoft\Validator\Rule\HasLength;
use Yiisoft\Validator\Rule\HasLengthHandler;

final class HasLengthHandlerTest extends AbstractRuleValidatorTest
{
    public function failedValidationProvider(): array
    {
        $defaultRule = new HasLength(min: 25);
        $errors = [new Error('This value must be a string.')];
        $lessThanMinErrors = [new Error('This value must contain at least {min, number} {min, plural, one{character} other{characters}}.', parameters: ['min' => 25])];
        $notExactlyErrors = [new Error('This value must contain exactly {exactly, number} {exactly, plural, one{character} other{characters}}.' , parameters: ['exactly' => 25])];

        return [
            [$defaultRule, ...$this->createValueAndErrorsPair(['not a string'], $errors)],
            [$defaultRule, ...$this->createValueAndErrorsPair(new stdClass(), $errors)],
            [$defaultRule, ...$this->createValueAndErrorsPair(true, $errors)],
            [$defaultRule, ...$this->createValueAndErrorsPair(false, $errors)],

            [
                new HasLength(max: 25),
                ...$this->createValueAndErrorsPair(
                    str_repeat('x', 1250),
                [new Error('This value must contain at most {max, number} {max, plural, one{character} other{characters}}.', parameters: ['max' => 25])]
                )
            ],
            [new HasLength(exactly: 25), ...$this->createValueAndErrorsPair(str_repeat('x', 125), $notExactlyErrors)],

            [new HasLength(exactly: 25), ...$this->createValueAndErrorsPair('', $notExactlyErrors)],
            [
                new HasLength(min: 10, max: 25),
                ...$this->createValueAndErrorsPair(
                    str_repeat('x', 5),
                [new Error('This value must contain at least {min, number} {min, plural, one{character} other{characters}}.', parameters: ['min' => 10])]
                ),
            ],
            [new HasLength(min: 25), ...$this->createValueAndErrorsPair(str_repeat('x', 13), $lessThanMinErrors)],
            [new HasLength(min: 25), ...$this->createValueAndErrorsPair('', $lessThanMinErrors)],
        ];
    }

    public function passedValidationProvider(): array
    {
        return [
            [new HasLength(exactly: 25), str_repeat('x', 25)],
            [new HasLength(exactly: 25), str_repeat('€', 25)],

            [new HasLength(min: 25), str_repeat('x', 125)],
            [new HasLength(min: 25), str_repeat('€', 25)],

            [new HasLength(max: 25), str_repeat('x', 25)],
            [new HasLength(max: 25), str_repeat('Ä', 24)],
            [new HasLength(max: 25), ''],

            [new HasLength(min: 10, max: 25), str_repeat('x', 15)],
            [new HasLength(min: 10, max: 25), str_repeat('x', 10)],
            [new HasLength(min: 10, max: 25), str_repeat('x', 20)],
            [new HasLength(min: 10, max: 25), str_repeat('x', 25)],

            [new HasLength(min: 1), str_repeat('x', 5)],
            [new HasLength(max: 100), str_repeat('x', 5)],
        ];
    }

    public function customErrorMessagesProvider(): array
    {
        $rule = new HasLength(
            min: 3,
            max: 5,
            message: 'is not string error',
            lessThanMinMessage: 'is too short test',
            greaterThanMaxMessage: 'is too long test'
        );

        return [
            [$rule,...$this->createValueAndErrorsPair( null, [new Error('is not string error')])],
            [$rule,...$this->createValueAndErrorsPair( str_repeat('x', 1), [new Error('is too short test', parameters: ['min' => 3])])],
            [$rule, ...$this->createValueAndErrorsPair(str_repeat('x', 6), [new Error('is too long test', parameters: ['max' => 5])])],
        ];
    }

    protected function getRuleHandler(): HasLengthHandler
    {
        return new HasLengthHandler();
    }
}
