<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use Yiisoft\Validator\Error;
use Yiisoft\Validator\Rule\CompareTo;
use Yiisoft\Validator\Rule\CompareHandler;
use Yiisoft\Validator\Rule\RuleHandlerInterface;

final class CompareToHandlerTest extends AbstractRuleValidatorTest
{
    public function failedValidationProvider(): array
    {
        $value = 100;
        $messageEqual = 'Value must be equal to "{targetValueOrAttribute}".';
        $messageNotEqual = 'Value must not be equal to "{targetValueOrAttribute}".';
        $messageGreaterThan = 'Value must be greater than "{targetValueOrAttribute}".';
        $messageGreaterOrEqualThan = 'Value must be greater than or equal to "{targetValueOrAttribute}".';
        $messageLessThan = 'Value must be less than "{targetValueOrAttribute}".';
        $messageLessOrEqualThan = 'Value must be less than or equal to "{targetValueOrAttribute}".';

        return [
            [
                new CompareTo($value),
                101,
                [new Error($this->formatMessage($messageEqual, ['targetValueOrAttribute' => $value]))],
            ],

            [
                new CompareTo($value, operator: '==='),
                $value + 1,
                [new Error($this->formatMessage($messageEqual, ['targetValueOrAttribute' => $value]))],
            ],
            [
                new CompareTo(null, 'attribute', operator: '==='),
                $value + 1,
                [new Error($this->formatMessage($messageEqual, ['targetValueOrAttribute' => $value]))],
            ],

            [
                new CompareTo($value, operator: '!='),
                $value,
                [new Error($this->formatMessage($messageNotEqual, ['targetValueOrAttribute' => $value]))],
            ],
            [
                new CompareTo($value, operator: '!='),
                (string)$value,
                [new Error($this->formatMessage($messageNotEqual, ['targetValueOrAttribute' => $value]))],
            ],
            [
                new CompareTo($value, operator: '!='),
                (float)$value,
                [new Error($this->formatMessage($messageNotEqual, ['targetValueOrAttribute' => $value]))],
            ],

            [
                new CompareTo($value, operator: '!=='),
                $value,
                [new Error($this->formatMessage($messageNotEqual, ['targetValueOrAttribute' => $value]))],
            ],
            [
                new CompareTo($value, operator: '!=='),
                (string)$value,
                [new Error($this->formatMessage($messageNotEqual, ['targetValueOrAttribute' => $value]))],
            ],
            [
                new CompareTo($value, operator: '!=='),
                (float)$value,
                [new Error($this->formatMessage($messageNotEqual, ['targetValueOrAttribute' => $value]))],
            ],

            [
                new CompareTo($value, operator: '>'),
                $value,
                [new Error($this->formatMessage($messageGreaterThan, ['targetValueOrAttribute' => $value]))],
            ],
            [
                new CompareTo($value, operator: '>'),
                $value - 1,
                [new Error($this->formatMessage($messageGreaterThan, ['targetValueOrAttribute' => $value]))],
            ],

            [
                new CompareTo($value, operator: '>='),
                $value - 1,
                [new Error($this->formatMessage($messageGreaterOrEqualThan, ['targetValueOrAttribute' => $value]))],
            ],

            [
                new CompareTo($value, operator: '<'),
                $value,
                [new Error($this->formatMessage($messageLessThan, ['targetValueOrAttribute' => $value]))],
            ],
            [
                new CompareTo($value, operator: '<'),
                $value + 1,
                [new Error($this->formatMessage($messageLessThan, ['targetValueOrAttribute' => $value]))],
            ],

            [
                new CompareTo($value, operator: '<='),
                $value + 1,
                [new Error($this->formatMessage($messageLessOrEqualThan, ['targetValueOrAttribute' => $value]))],
            ],
            [
                new CompareTo(null, 'attribute', operator: '<='),
                $value + 1,
                [new Error($this->formatMessage($messageLessOrEqualThan, ['targetValueOrAttribute' => $value]))],
            ],
        ];
    }

    public function passedValidationProvider(): array
    {
        $value = 100;

        return [
            [new CompareTo($value), $value],
            [new CompareTo(null, 'attribute'), $value],
            [new CompareTo($value), (string)$value],

            [new CompareTo($value, operator: '==='), $value],
            [new CompareTo($value, operator: '==='), (string)$value],
            [new CompareTo($value, operator: '==='), (float)$value],

            [new CompareTo($value, operator: '!='), $value + 0.00001],
            [new CompareTo($value, operator: '!='), false],

            [new CompareTo($value, operator: '!=='), false],

            [new CompareTo($value, operator: '>'), $value + 1],

            [new CompareTo($value, operator: '>='), $value],
            [new CompareTo($value, operator: '>='), $value + 1],
            [new CompareTo($value, operator: '<'), $value - 1],

            [new CompareTo($value, operator: '<='), $value],
            [new CompareTo($value, operator: '<='), $value - 1],
            [new CompareTo(null, 'attribute', operator: '<='), $value - 1],
        ];
    }

    public function customErrorMessagesProvider(): array
    {
        return [
            [
                new CompareTo(100, message: 'Custom error'),
                101,
                [new Error('Custom error')],
            ],
        ];
    }

    protected function getValidator(): RuleHandlerInterface
    {
        return new CompareHandler();
    }
}
