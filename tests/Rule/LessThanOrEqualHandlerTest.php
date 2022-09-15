<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use Yiisoft\Validator\Error;
use Yiisoft\Validator\Rule\CompareHandler;
use Yiisoft\Validator\Rule\LessThanOrEqual;
use Yiisoft\Validator\RuleHandlerInterface;

final class LessThanOrEqualHandlerTest extends AbstractRuleValidatorTest
{
    public function failedValidationProvider(): array
    {
        $value = 100;
        $errors = [
            new Error('Value must be less than or equal to "{targetValueOrAttribute}".', parameters: [
                'targetValue' => 100,
                'targetAttribute' => null,
                'targetValueOrAttribute' => 100,
            ]),
        ];

        return [
            [new LessThanOrEqual($value), ...$this->createValueAndErrorsPair(101, $errors)],
            [new LessThanOrEqual($value), ...$this->createValueAndErrorsPair('101', $errors)],
        ];
    }

    public function passedValidationProvider(): array
    {
        $value = 100;

        return [
            [new LessThanOrEqual(101), $value],
            [new LessThanOrEqual(100), $value],
            [new LessThanOrEqual('101'), (string)$value],
        ];
    }

    public function customErrorMessagesProvider(): array
    {
        return [
            [
                new LessThanOrEqual(100, message: 'Custom error'),
                ...$this->createValueAndErrorsPair(
                    101,
                    [new Error('Custom error', parameters: ['targetValue' => 100, 'targetAttribute' => null, 'targetValueOrAttribute' => 100])]
                ),
            ],
        ];
    }

    protected function getRuleHandler(): RuleHandlerInterface
    {
        return new CompareHandler();
    }
}
