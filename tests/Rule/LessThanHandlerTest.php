<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use Yiisoft\Validator\Error;
use Yiisoft\Validator\Rule\CompareHandler;
use Yiisoft\Validator\Rule\LessThan;
use Yiisoft\Validator\RuleHandlerInterface;

final class LessThanHandlerTest extends AbstractRuleValidatorTest
{
    public function failedValidationProvider(): array
    {
        $value = 100;
        $message = 'Value must be less than "{targetValueOrAttribute}".';
        $errors = [new Error($message, parameters: ['targetValue' => 100, 'targetAttribute' => null, 'targetValueOrAttribute' => 100])];

        return [
            [new LessThan($value), ...$this->createValueAndErrorsPair(100, $errors)],
            [new LessThan($value), ...$this->createValueAndErrorsPair('101', $errors)],
        ];
    }

    public function passedValidationProvider(): array
    {
        $value = 100;

        return [
            [new LessThan(101), $value],
            [new LessThan('101'), (string)$value],
        ];
    }

    public function customErrorMessagesProvider(): array
    {
        return [
            [
                new LessThan(100, message: 'Custom error'), ...$this->createValueAndErrorsPair(
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
