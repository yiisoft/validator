<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use Yiisoft\Validator\DataSet\ArrayDataSet;
use Yiisoft\Validator\Error;
use Yiisoft\Validator\Rule\CompareTo;
use Yiisoft\Validator\Rule\CompareHandler;
use Yiisoft\Validator\RuleHandlerInterface;
use Yiisoft\Validator\Tests\Stub\FakeValidatorFactory;
use Yiisoft\Validator\Tests\Stub\TranslatorFactory;
use Yiisoft\Validator\ValidationContext;

final class CompareToHandlerTest extends AbstractRuleValidatorTest
{
    public function failedValidationProvider(): array
    {
        $value = 100;
        $messageEqual = 'Value must be equal to "100".';
        $messageNotEqual = 'Value must not be equal to "100".';
        $messageGreaterThan = 'Value must be greater than "100".';
        $messageGreaterOrEqualThan = 'Value must be greater than or equal to "100".';
        $messageLessThan = 'Value must be less than "100".';
        $messageLessOrEqualThan = 'Value must be less than or equal to "100".';

        return [
            [new CompareTo($value), 101, [new Error($messageEqual)]],

            [new CompareTo($value, operator: '==='), $value + 1, [new Error($messageEqual)]],
            [new CompareTo(null, 'attribute', operator: '==='), $value + 1, [new Error($messageEqual)]],

            [new CompareTo($value, operator: '!='), $value, [new Error($messageNotEqual)]],
            [new CompareTo($value, operator: '!='), (string)$value, [new Error($messageNotEqual)]],
            [new CompareTo($value, operator: '!='), (float)$value, [new Error($messageNotEqual)]],

            [new CompareTo($value, operator: '!=='), $value, [new Error($messageNotEqual)]],
            [new CompareTo($value, operator: '!=='), (string)$value, [new Error($messageNotEqual)]],
            [new CompareTo($value, operator: '!=='), (float)$value, [new Error($messageNotEqual)]],

            [new CompareTo($value, operator: '>'), $value, [new Error($messageGreaterThan)]],
            [new CompareTo($value, operator: '>'), $value - 1, [new Error($messageGreaterThan)]],

            [new CompareTo($value, operator: '>='), $value - 1, [new Error($messageGreaterOrEqualThan)]],

            [new CompareTo($value, operator: '<'), $value, [new Error($messageLessThan)]],
            [new CompareTo($value, operator: '<'), $value + 1, [new Error($messageLessThan)]],

            [new CompareTo($value, operator: '<='), $value + 1, [new Error($messageLessOrEqualThan)]],
            [new CompareTo(null, 'attribute', operator: '<='), $value + 1, [new Error($messageLessOrEqualThan)]],
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
            [new CompareTo(100, message: 'Custom error'), 101, [new Error('Custom error')]],
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
            (new TranslatorFactory())->create(),
            new ArrayDataSet(['attribute' => 100, 'width_repeat' => 100]),
            'width'
        );
    }
}
