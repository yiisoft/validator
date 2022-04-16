<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule\InRange;

use ArrayObject;
use Yiisoft\Validator\Error;
use Yiisoft\Validator\Rule\InRange\InRange;
use Yiisoft\Validator\Rule\InRange\InRangeHandler;
use Yiisoft\Validator\Rule\RuleHandlerInterface;
use Yiisoft\Validator\Tests\Rule\AbstractRuleValidatorTest;

final class InRangeHandlerTest extends AbstractRuleValidatorTest
{
    public function failedValidationProvider(): array
    {
        $rule = new InRange(range(1, 10));
        $ruleStrict = new InRange(range(1, 10), strict: true);
        $ruleNot = new InRange(range(1, 10), not: true);

        $message = $rule->message;
        $errors = [new Error($message, [])];

        return [
            [$rule, 0, $errors],
            [$rule, 11, $errors],
            [$rule, 5.5, $errors],

            [$rule, null, $errors],
            [$rule, '0', $errors],
            [$rule, 0, $errors],
            [$rule, '', $errors],

            [$ruleStrict, '1', $errors],
            [$ruleStrict, '10', $errors],
            [$ruleStrict, '5.5', $errors],
            [$ruleStrict, ['1', '2', '3', '4', '5', '6'], $errors],
            [$ruleStrict, ['1', '2', '3', 4, 5, 6], $errors],

            [$ruleNot, 1, $errors],
            [$ruleNot, 10, $errors],
            [$ruleNot, '10', $errors],
            [$ruleNot, '5', $errors],
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
            [
                new InRange(range(1, 10), message: 'Custom error'),
                15,
                [new Error('Custom error', [])],
            ],
        ];
    }

    protected function getValidator(): RuleHandlerInterface
    {
        return new InRangeHandler();
    }
}
