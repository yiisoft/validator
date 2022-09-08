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
        $message = 'Value must not be equal to "100".';

        return [
            [new NotEqual($value), $value, [new Error($message)]],
            [new NotEqual($value, strict: true), $value, [new Error($message)]],
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
            [new NotEqual(100, message: 'Custom error'), 100, [new Error('Custom error')]],
        ];
    }

    protected function getRuleHandler(): RuleHandlerInterface
    {
        return new CompareHandler($this->getTranslator());
    }
}
