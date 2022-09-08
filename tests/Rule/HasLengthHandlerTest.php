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
        $message = 'This value must be a string.';
        $greaterThanMaxMessage = 'This value must contain at most 25 characters.';
        $notExactlyMessage = 'This value must contain exactly 25 characters.';
        $lessThanMinMessage = 'This value must contain at least 25 characters.';

        return [
            [$defaultRule, ['not a string'], [new Error($message)]],
            [$defaultRule, new stdClass(), [new Error($message)]],
            [$defaultRule, true, [new Error($message)]],
            [$defaultRule, false, [new Error($message)]],

            [new HasLength(max: 25), str_repeat('x', 1250), [new Error($greaterThanMaxMessage)]],
            [new HasLength(exactly: 25), str_repeat('x', 125), [new Error($notExactlyMessage)]],

            [new HasLength(exactly: 25), '', [new Error($notExactlyMessage)]],
            [
                new HasLength(min: 10, max: 25),
                str_repeat('x', 5),
                [new Error('This value must contain at least 10 characters.')]
            ],
            [new HasLength(min: 25), str_repeat('x', 13), [new Error($lessThanMinMessage)]],
            [new HasLength(min: 25), '', [new Error($lessThanMinMessage)]],
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
            [$rule, null, [new Error('is not string error')]],
            [$rule, str_repeat('x', 1), [new Error('is too short test')],],
            [$rule, str_repeat('x', 6), [new Error('is too long test')]],
        ];
    }

    protected function getRuleHandler(): HasLengthHandler
    {
        return new HasLengthHandler($this->getTranslator());
    }
}
