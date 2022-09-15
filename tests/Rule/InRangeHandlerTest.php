<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use ArrayObject;
use Yiisoft\Validator\Error;
use Yiisoft\Validator\Rule\InRange;
use Yiisoft\Validator\Rule\InRangeHandler;
use Yiisoft\Validator\RuleHandlerInterface;

final class InRangeHandlerTest extends AbstractRuleValidatorTest
{
    public function failedValidationProvider(): array
    {
        $rule = new InRange(range(1, 10));
        $ruleStrict = new InRange(range(1, 10), strict: true);
        $ruleNot = new InRange(range(1, 10), not: true);
        $errors = [new Error('This value is invalid.')];

        return [
            [$rule, ...$this->createValueAndErrorsPair(0, $errors)],
            [$rule, ...$this->createValueAndErrorsPair(11, $errors)],
            [$rule, ...$this->createValueAndErrorsPair(5.5, $errors)],

            [$rule, ...$this->createValueAndErrorsPair(null, $errors)],
            [$rule, ...$this->createValueAndErrorsPair('0', $errors)],
            [$rule, ...$this->createValueAndErrorsPair(0, $errors)],
            [$rule, ...$this->createValueAndErrorsPair('', $errors)],

            [$ruleStrict, ...$this->createValueAndErrorsPair('1', $errors)],
            [$ruleStrict, ...$this->createValueAndErrorsPair('10', $errors)],
            [$ruleStrict, ...$this->createValueAndErrorsPair('5.5', $errors)],
            [$ruleStrict, ...$this->createValueAndErrorsPair(['1', '2', '3', '4', '5', '6'], $errors)],
            [$ruleStrict, ...$this->createValueAndErrorsPair(['1', '2', '3', 4, 5, 6], $errors)],

            [$ruleNot, ...$this->createValueAndErrorsPair(1, $errors)],
            [$ruleNot, ...$this->createValueAndErrorsPair(10, $errors)],
            [$ruleNot, ...$this->createValueAndErrorsPair('10', $errors)],
            [$ruleNot, ...$this->createValueAndErrorsPair('5', $errors)],
        ];
    }

    public function passedValidationProvider(): array
    {
        $rule = new InRange(range(1, 10));
        $ruleStrict = new InRange(range(1, 10), strict: true);
        $ruleNot = new InRange(range(1, 10), not: true);

        return [
            [$rule, 1],
            [$rule, 10],
            [$rule, '10'],
            [$rule, '5'],

            [new InRange([['a'], ['b']]), ['a']],
            [new InRange(new ArrayObject(['a', 'b'])), 'a'],

            [$ruleStrict, 1],
            [$ruleStrict, 5],
            [$ruleStrict, 10],

            [$ruleNot, 0],
            [$ruleNot, 11],
            [$ruleNot, 5.5],
        ];
    }

    public function customErrorMessagesProvider(): array
    {
        return [
            [new InRange(range(1, 10), message: 'Custom error'), ...$this->createValueAndErrorsPair(15, [new Error('Custom error')])],
        ];
    }

    protected function getRuleHandler(): RuleHandlerInterface
    {
        return new InRangeHandler();
    }
}
