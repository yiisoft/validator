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
        $message = 'Value must be greater than or equal to "{targetValueOrAttribute}".';
        $errors = [new Error($message, parameters: ['targetValue' => 100, 'targetAttribute' => null, 'targetValueOrAttribute' => 100])];

        return [
            [new GreaterThanOrEqual($value),...$this->createValueAndErrorsPair( 99, $errors)],
            [new GreaterThanOrEqual($value), ...$this->createValueAndErrorsPair('99', $errors)],
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
            [new GreaterThanOrEqual(100, message: 'Custom error'), ...$this->createValueAndErrorsPair(99, [new Error('Custom error', parameters: ['targetValue' => 100, 'targetAttribute' => null, 'targetValueOrAttribute' => 100])])],
        ];
    }

    protected function getRuleHandler(): RuleHandlerInterface
    {
        return new CompareHandler();
    }
}
