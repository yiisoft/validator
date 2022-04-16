<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule\Subset;

use ArrayObject;
use Yiisoft\Validator\Error;
use Yiisoft\Validator\Rule\RuleValidatorInterface;
use Yiisoft\Validator\Rule\Subset\Subset;
use Yiisoft\Validator\Rule\Subset\SubsetHandler;
use Yiisoft\Validator\Tests\Rule\AbstractRuleValidatorTest;

final class SubsetHandlerTest extends AbstractRuleValidatorTest
{
    public function failedValidationProvider(): array
    {
        $range = range(1, 10);
        $rule = new Subset($range);
        $rangeToShow = implode(', ', array_map(fn (int $value) => "\"{$value}\"", $range));

        return [
            [$rule, [0, 1, 2], [new Error($rule->subsetMessage, ['values' => $rangeToShow])]],
            [$rule, [10, 11, 12], [new Error($rule->subsetMessage, ['values' => $rangeToShow])]],
        ];
    }

    public function passedValidationProvider(): array
    {
        $rule = new Subset(range(1, 10));

        return [
            [$rule, []],
            [$rule, [1, 2, 3, 4, 5]],
            [$rule, [6, 7, 8, 9, 10]],
            [$rule, ['1', '2', '3', 4, 5, 6]],

            [new Subset(['a', 'b', 'c']), ['a', 'b']],
            [new Subset(['a', 'b', 'c']), new ArrayObject(['a', 'b'])],
        ];
    }

    public function customErrorMessagesProvider(): array
    {
        return [
            [
                new Subset(['a'], subsetMessage: 'Custom error'),
                ['2'],
                [new Error('Custom error', ['values' => '"a"'])],
            ],
        ];
    }

    protected function getValidator(): RuleValidatorInterface
    {
        return new SubsetHandler();
    }
}
