<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use Yiisoft\Validator\Error;
use Yiisoft\Validator\Rule\CompareHandler;
use Yiisoft\Validator\Rule\GreaterThanOrEqual;
use Yiisoft\Validator\RuleHandlerInterface;

final class GreaterThanOrEqualHandlerTest extends AbstractRuleValidatorTest
{
    public function failedValidationProvider(): array
    {
        $value = 100;
        $messageGreaterThanOrEqual = 'Value must be greater than or equal to "{targetValueOrAttribute}".';

        return [
            [
                new GreaterThanOrEqual($value),
                99,
                [new Error($this->formatMessage($messageGreaterThanOrEqual, ['targetValueOrAttribute' => $value]))],
            ],
            [
                new GreaterThanOrEqual($value),
                '99',
                [new Error($this->formatMessage($messageGreaterThanOrEqual, ['targetValueOrAttribute' => $value]))],
            ],
        ];
    }

    public function passedValidationProvider(): array
    {
        $value = 100;

        return [
            [new GreaterThanOrEqual(99), $value],
            [new GreaterThanOrEqual('100'), (string)$value],
        ];
    }

    public function customErrorMessagesProvider(): array
    {
        return [
            [
                new GreaterThanOrEqual(100, message: 'Custom error'),
                99,
                [new Error('Custom error')],
            ],
        ];
    }

    protected function getRuleHandler(): RuleHandlerInterface
    {
        return new CompareHandler();
    }
}
