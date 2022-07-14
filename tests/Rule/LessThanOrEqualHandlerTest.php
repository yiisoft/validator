<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use Yiisoft\Validator\Error;
use Yiisoft\Validator\Rule\LessThanOrEqual;
use Yiisoft\Validator\Rule\LessThanOrEqualHandler;
use Yiisoft\Validator\Rule\RuleHandlerInterface;

final class LessThanOrEqualHandlerTest extends AbstractRuleValidatorTest
{
    public function failedValidationProvider(): array
    {
        $value = 100;
        $messageLessThanOrEqual = 'Value must be less than or equal to "{targetValueOrAttribute}".';

        return [
            [
                new LessThanOrEqual($value),
                101,
                [new Error($this->formatMessage($messageLessThanOrEqual, ['targetValueOrAttribute' => $value]))],
            ],
            [
                new LessThanOrEqual($value),
                '101',
                [new Error($this->formatMessage($messageLessThanOrEqual, ['targetValueOrAttribute' => $value]))],
            ],
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
                101,
                [new Error('Custom error')],
            ],
        ];
    }

    protected function getValidator(): RuleHandlerInterface
    {
        return new LessThanOrEqualHandler();
    }
}
