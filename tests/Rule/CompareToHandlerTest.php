<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use Yiisoft\Validator\DataSet\ArrayDataSet;
use Yiisoft\Validator\Error;
use Yiisoft\Validator\Rule\CompareTo;
use Yiisoft\Validator\Rule\CompareHandler;
use Yiisoft\Validator\RuleHandlerInterface;
use Yiisoft\Validator\Tests\Stub\FakeValidatorFactory;
use Yiisoft\Validator\ValidationContext;

final class CompareToHandlerTest extends AbstractRuleValidatorTest
{
    public function failedValidationProvider(): array
    {
        $value = 100;

        $errors = [
            new Error('Value must be equal to "{targetValueOrAttribute}".', parameters: [
                'targetValue' => 100,
                'targetAttribute' => null,
                'targetValueOrAttribute' => 100,
            ]),
        ];
        $errors1 = [
            new Error('Value must not be equal to "{targetValueOrAttribute}".', parameters: [
                'targetValue' => 100,
                'targetAttribute' => null,
                'targetValueOrAttribute' => 100,
            ]),
        ];
        $errors2 = [
            new Error('Value must be greater than "{targetValueOrAttribute}".', parameters: [
                'targetValue' => 100,
                'targetAttribute' => null,
                'targetValueOrAttribute' => 100,
            ]),
        ];
        $errors3 = [
            new Error('Value must be greater than or equal to "{targetValueOrAttribute}".', parameters: [
                'targetValue' => 100,
                'targetAttribute' => null,
                'targetValueOrAttribute' => 100,
            ]),
        ];
        $errors4 = [
            new Error('Value must be less than "{targetValueOrAttribute}".', parameters: [
                'targetValue' => 100,
                'targetAttribute' => null,
                'targetValueOrAttribute' => 100,
            ]),
        ];
        $errors5 = [
            new Error('Value must be less than or equal to "{targetValueOrAttribute}".', parameters: [
                'targetValue' => 100,
                'targetAttribute' => null,
                'targetValueOrAttribute' => 100,
            ]),
        ];
        return [
            [new CompareTo($value), ...$this->createValueAndErrorsPair(101, $errors)],

            [new CompareTo($value, operator: '==='), ...$this->createValueAndErrorsPair($value + 1, $errors)],
            [
                new CompareTo(null, 'attribute', operator: '==='),
                ...$this->createValueAndErrorsPair(
                    $value + 1,
                    [
                        new Error('Value must be equal to "{targetValueOrAttribute}".', parameters: [
                            'targetValue' => null,
                            'targetAttribute' => 'attribute',
                            'targetValueOrAttribute' => 100,
                        ]),
                    ]
                ),
            ],

            [new CompareTo($value, operator: '!='), ...$this->createValueAndErrorsPair($value, $errors1)],
            [new CompareTo($value, operator: '!='), ...$this->createValueAndErrorsPair((string)$value, $errors1)],
            [new CompareTo($value, operator: '!='), ...$this->createValueAndErrorsPair((float)$value, $errors1)],

            [new CompareTo($value, operator: '!=='), ...$this->createValueAndErrorsPair($value, $errors1)],
            [new CompareTo($value, operator: '!=='), ...$this->createValueAndErrorsPair((string)$value, $errors1)],
            [new CompareTo($value, operator: '!=='), ...$this->createValueAndErrorsPair((float)$value, $errors1)],

            [new CompareTo($value, operator: '>'), ...$this->createValueAndErrorsPair($value, $errors2)],
            [new CompareTo($value, operator: '>'), ...$this->createValueAndErrorsPair($value - 1, $errors2)],

            [new CompareTo($value, operator: '>='), ...$this->createValueAndErrorsPair($value - 1, $errors3)],

            [new CompareTo($value, operator: '<'), ...$this->createValueAndErrorsPair($value, $errors4)],
            [new CompareTo($value, operator: '<'), ...$this->createValueAndErrorsPair($value + 1, $errors4)],

            [new CompareTo($value, operator: '<='),...$this->createValueAndErrorsPair($value + 1, $errors5)],
            [
                new CompareTo(null, 'attribute', operator: '<='),
                ...$this->createValueAndErrorsPair(
                    $value + 1,
                    [
                        new Error('Value must be less than or equal to "{targetValueOrAttribute}".', parameters: [
                            'targetValue' => null,
                            'targetAttribute' => 'attribute',
                            'targetValueOrAttribute' => 100,
                        ]),
                    ]
                ),
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
            [new CompareTo(100, message: 'Custom error'), ...$this->createValueAndErrorsPair(101, [new Error('Custom error', parameters: [
                'targetValue' => 100,
                'targetAttribute' => null,
                'targetValueOrAttribute' => 100,
            ])])],
        ];
    }

    protected function getRuleHandler(): RuleHandlerInterface
    {
        return new CompareHandler();
    }

    protected function getValidationContext(): ValidationContext
    {
        $validator = FakeValidatorFactory::make();
        return new ValidationContext(
            $validator,
            new ArrayDataSet(['attribute' => 100, 'width_repeat' => 100]),
            'width'
        );
    }
}
