<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use Yiisoft\Validator\Error;
use Yiisoft\Validator\Rule\CompareHandler;
use Yiisoft\Validator\Rule\NotEqual;
use Yiisoft\Validator\RuleHandlerInterface;

final class NotEqualHandlerTest extends AbstractRuleValidatorTest
{
    public function failedValidationProvider(): array
    {
        $value = 100;
        $errors = [new Error('Value must not be equal to "{targetValueOrAttribute}".', parameters: [
            'targetValue' => $value,
            'targetAttribute' => null,
            'targetValueOrAttribute' => $value,
        ])];

        return [
            [new NotEqual($value), ...$this->createValueAndErrorsPair($value, $errors)],
            [new NotEqual($value, strict: true),...$this->createValueAndErrorsPair($value, $errors)],
        ];
    }

    public function passedValidationProvider(): array
    {
        $value = 100;

        return [
            [new NotEqual($value), $value + 1],
            [new NotEqual($value, strict: true), '101'],
        ];
    }

    public function customErrorMessagesProvider(): array
    {
        return [
            [
                new NotEqual(100, message: 'Custom error'),
                ...$this->createValueAndErrorsPair(100, [
                    new Error('Custom error', parameters: [
                        'targetValue' => 100,
                        'targetAttribute' => null,
                        'targetValueOrAttribute' => 100,
                    ]),
                ]),
            ],
        ];
    }

    protected function getRuleHandler(): RuleHandlerInterface
    {
        return new CompareHandler();
    }
}
