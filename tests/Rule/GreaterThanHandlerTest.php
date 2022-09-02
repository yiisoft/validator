<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use Yiisoft\Validator\Error;
use Yiisoft\Validator\Rule\CompareHandler;
use Yiisoft\Validator\Rule\GreaterThan;
use Yiisoft\Validator\RuleHandlerInterface;

final class GreaterThanHandlerTest extends AbstractRuleValidatorTest
{
    public function failedValidationProvider(): array
    {
        $value = 100;
        $message = 'Value must be greater than "100".';

        return [
            [new GreaterThan($value), 99, [new Error($message)]],
            [new GreaterThan($value), '100', [new Error($message)]],
        ];
    }

    public function passedValidationProvider(): array
    {
        $value = 100;

        return [
            [new GreaterThan(99), $value],
            [new GreaterThan('99'), (string)$value],
        ];
    }

    public function customErrorMessagesProvider(): array
    {
        return [
            [new GreaterThan(100, message: 'Custom error'), 99, [new Error('Custom error')]],
        ];
    }

    protected function getRuleHandler(): RuleHandlerInterface
    {
        return new CompareHandler($this->getTranslator());
    }
}
