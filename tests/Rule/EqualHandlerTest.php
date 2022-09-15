<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use Yiisoft\Validator\Error;
use Yiisoft\Validator\Rule\CompareHandler;
use Yiisoft\Validator\Rule\Equal;
use Yiisoft\Validator\RuleHandlerInterface;

final class EqualHandlerTest extends AbstractRuleValidatorTest
{
    public function failedValidationProvider(): array
    {
        $value = 100;
        $errors = [
            new Error('Value must be equal to "{targetValueOrAttribute}".', parameters: [
                'targetValue' => $value,
                'targetAttribute' => null,
                'targetValueOrAttribute' => $value,
            ]),
        ];
        return [
            [new Equal($value), ...$this->createValueAndErrorsPair(101, $errors)],
            [new Equal($value, strict: true), ...$this->createValueAndErrorsPair($value + 1, $errors)],
        ];
    }

    public function passedValidationProvider(): array
    {
        $value = 100;

        return [
            [new Equal($value), $value],
            [new Equal($value), (string) $value],
        ];
    }

    public function customErrorMessagesProvider(): array
    {
        return [
            [new Equal(100, message: 'Custom error'),
                ...$this->createValueAndErrorsPair(
                    101,
                    [new Error('Custom error', parameters: [
                        'targetValue' => 100,
                        'targetAttribute' => null,
                        'targetValueOrAttribute' => 100,
                    ])]
                ), ],
        ];
    }

    protected function getRuleHandler(): RuleHandlerInterface
    {
        return new CompareHandler();
    }
}
