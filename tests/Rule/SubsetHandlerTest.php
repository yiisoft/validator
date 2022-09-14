<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use ArrayObject;
use Yiisoft\Validator\Error;
use Yiisoft\Validator\RuleHandlerInterface;
use Yiisoft\Validator\Rule\Subset;
use Yiisoft\Validator\Rule\SubsetHandler;

final class SubsetHandlerTest extends AbstractRuleValidatorTest
{
    public function failedValidationProvider(): array
    {
        $range = range(1, 10);
        $rule = new Subset($range);
        $subsetMessage = 'Values must be ones of "1", "2", "3", "4", "5", "6", "7", "8", "9", "10".';

        return [
            [$rule, [0, 1, 2], [new Error($subsetMessage)]],
            [$rule, [10, 11, 12], [new Error($subsetMessage)]],
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
            [new Subset(['a'], subsetMessage: 'Custom error'), ['2'], [new Error('Custom error')]],
        ];
    }

    protected function getRuleHandler(): RuleHandlerInterface
    {
        return new SubsetHandler();
    }
}
