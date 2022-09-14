<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use Yiisoft\Validator\Error;
use Yiisoft\Validator\Rule\CompareHandler;
use Yiisoft\Validator\Rule\LessThanOrEqual;
use Yiisoft\Validator\RuleHandlerInterface;

final class LessThanOrEqualHandlerTest extends AbstractRuleValidatorTest
{
    public function failedValidationProvider(): array
    {
        $value = 100;
        $message = 'Value must be less than or equal to "100".';

        return [
            [new LessThanOrEqual($value), 101, [new Error($message)]],
            [new LessThanOrEqual($value), '101', [new Error($message)]],
        ];
    }

    public function passedValidationProvider(): array
    {
        $value = 100;

        return [
            [new LessThanOrEqual(101), $value],
            [new LessThanOrEqual(100), $value],
            [new LessThanOrEqual('101'), (string)$value],
        ];
    }

    public function customErrorMessagesProvider(): array
    {
        return [
            [new LessThanOrEqual(100, message: 'Custom error'), 101, [new Error('Custom error')]],
        ];
    }

    protected function getRuleHandler(): RuleHandlerInterface
    {
        return new CompareHandler();
    }
}
