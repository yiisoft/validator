<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use Yiisoft\Validator\Error;
use Yiisoft\Validator\Rule\CompareHandler;
use Yiisoft\Validator\Rule\LessThan;
use Yiisoft\Validator\RuleHandlerInterface;

final class LessThanHandlerTest extends AbstractRuleValidatorTest
{
    public function failedValidationProvider(): array
    {
        $value = 100;
        $message = 'Value must be less than "100".';

        return [
            [new LessThan($value), 100, [new Error($message)]],
            [new LessThan($value), '101', [new Error($message)]],
        ];
    }

    public function passedValidationProvider(): array
    {
        $value = 100;

        return [
            [new LessThan(101), $value],
            [new LessThan('101'), (string)$value],
        ];
    }

    public function customErrorMessagesProvider(): array
    {
        return [
            [new LessThan(100, message: 'Custom error'), 101, [new Error('Custom error')]],
        ];
    }

    protected function getRuleHandler(): RuleHandlerInterface
    {
        return new CompareHandler();
    }
}
