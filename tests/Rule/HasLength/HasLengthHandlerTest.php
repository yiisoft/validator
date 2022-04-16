<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule\HasLength;

use stdClass;
use Yiisoft\Validator\Error;
use Yiisoft\Validator\Rule\HasLength\HasLength;
use Yiisoft\Validator\Rule\HasLength\HasLengthHandler;
use Yiisoft\Validator\Tests\Rule\AbstractRuleValidatorTest;

final class HasLengthHandlerTest extends AbstractRuleValidatorTest
{
    public function failedValidationProvider(): array
    {
        $defaultConfig = new HasLength();

        return [
            [$defaultConfig, ['not a string'], [new Error($defaultConfig->message, [])]],
            [$defaultConfig, new stdClass(), [new Error($defaultConfig->message, [])]],
            [$defaultConfig, true, [new Error($defaultConfig->message, [])]],
            [$defaultConfig, false, [new Error($defaultConfig->message, [])]],

            [new HasLength(max: 25), str_repeat('x', 1250), [new Error($defaultConfig->tooLongMessage, ['max' => 25])]],
            [new HasLength(min: 25, max: 25), str_repeat('x', 125), [new Error($defaultConfig->tooLongMessage, ['max' => 25])]],

            [new HasLength(min: 25, max: 25), '', [new Error($defaultConfig->tooShortMessage, ['min' => 25])]],
            [new HasLength(min: 10, max: 25), str_repeat('x', 5), [new Error($defaultConfig->tooShortMessage, ['min' => 10])]],
            [new HasLength(min: 25), str_repeat('x', 13), [new Error($defaultConfig->tooShortMessage, ['min' => 25])]],
            [new HasLength(min: 25), '', [new Error($defaultConfig->tooShortMessage, ['min' => 25])]],
        ];
    }

    public function passedValidationProvider(): array
    {
        return [
            [new HasLength(), 'Just some string'],

            [new HasLength(min: 25, max: 25), str_repeat('x', 25)],
            [new HasLength(min: 25, max: 25), str_repeat('€', 25)],

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
            tooShortMessage: 'is too short test',
            tooLongMessage: 'is too long test'
        );

        return [
            [
                $rule,
                null,
                [new Error('is not string error', [])],
            ],
            [
                $rule,
                str_repeat('x', 1),
                [new Error('is too short test', ['min' => 3])],
            ],
            [
                $rule,
                str_repeat('x', 6),
                [new Error('is too long test', ['max' => 5])],
            ],
        ];
    }

    protected function getValidator(): HasLengthHandler
    {
        return new HasLengthHandler();
    }
}
