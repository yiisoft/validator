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
        $defaultConfig = new HasLength(min: 25);

        return [
            [$defaultConfig, ['not a string'], [new Error($defaultConfig->getMessage(), [])]],
            [$defaultConfig, new stdClass(), [new Error($defaultConfig->getMessage(), [])]],
            [$defaultConfig, true, [new Error($defaultConfig->getMessage(), [])]],
            [$defaultConfig, false, [new Error($defaultConfig->getMessage(), [])]],

            [
                new HasLength(max: 25),
                str_repeat('x', 1250),
                [new Error($this->formatMessage($defaultConfig->getGreaterThanMaxMessage(), ['max' => 25]))],
            ],
            [
                new HasLength(exactly: 25),
                str_repeat('x', 125),
                [new Error($this->formatMessage($defaultConfig->getNotExactlyMessage(), ['max' => 25]))],
            ],

            [
                new HasLength(exactly: 25),
                '',
                [new Error($this->formatMessage($defaultConfig->getNotExactlyMessage(), ['min' => 25]))],
            ],
            [
                new HasLength(min: 10, max: 25),
                str_repeat('x', 5),
                [new Error($this->formatMessage($defaultConfig->getLessThanMinMessage(), ['min' => 10]))],
            ],
            [
                new HasLength(min: 25),
                str_repeat('x', 13),
                [new Error($this->formatMessage($defaultConfig->getLessThanMinMessage(), ['min' => 25]))],
            ],
            [
                new HasLength(min: 25),
                '',
                [new Error($this->formatMessage($defaultConfig->getLessThanMinMessage(), ['min' => 25]))],
            ],
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
            [
                $rule,
                null,
                [new Error('is not string error', [])],
            ],
            [
                $rule,
                str_repeat('x', 1),
                [new Error($this->formatMessage('is too short test', ['min' => 3]))],
            ],
            [
                $rule,
                str_repeat('x', 6),
                [new Error($this->formatMessage('is too long test', ['max' => 5]))],
            ],
        ];
    }

    protected function getRuleHandler(): HasLengthHandler
    {
        return new HasLengthHandler();
    }
}
