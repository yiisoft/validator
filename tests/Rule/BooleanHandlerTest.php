<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use Yiisoft\Validator\Error;
use Yiisoft\Validator\Rule\Boolean;
use Yiisoft\Validator\Rule\BooleanHandler;
use Yiisoft\Validator\RuleHandlerInterface;

final class BooleanHandlerTest extends AbstractRuleValidatorTest
{
    public function failedValidationProvider(): array
    {
        $defaultRule = new Boolean();
        $defaultError = new Error(
            message: 'The value must be either "{true}" or "{false}".',
            parameters: ['true' => 1, 'false' => 0]
        );
        $booleanError = new Error(
            message:'The value must be either "{true}" or "{false}".',
            parameters: ['true' => 'true', 'false' => 'false']
        );

        return [
            [$defaultRule, ...$this->createValueAndErrorsPair('5', [$defaultError])],

            [$defaultRule, ...$this->createValueAndErrorsPair(null, [$defaultError])],
            [$defaultRule, ...$this->createValueAndErrorsPair([], [$defaultError])],


            [new Boolean(strict: true), ...$this->createValueAndErrorsPair(true, [$defaultError])],
            [new Boolean(strict: true), ...$this->createValueAndErrorsPair(false, [$defaultError])],

            [new Boolean(trueValue: true, falseValue: false, strict: true), ...$this->createValueAndErrorsPair('0', [$booleanError])],
            [new Boolean(trueValue: true, falseValue: false, strict: true), ...$this->createValueAndErrorsPair([], [$booleanError])],
        ];
    }

    public function passedValidationProvider(): array
    {
        return [
            [new Boolean(), true],
            [new Boolean(), false],

            [new Boolean(), '0'],
            [new Boolean(), '1'],

            [new Boolean(strict: true), '0'],
            [new Boolean(strict: true), '1'],

            [new Boolean(trueValue: true, falseValue: false, strict: true), true],
            [new Boolean(trueValue: true, falseValue: false, strict: true), false],
        ];
    }

    public function customErrorMessagesProvider(): array
    {
        return [
            [new Boolean(message: 'Custom error.'), ...$this->createValueAndErrorsPair(5, [new Error('Custom error.', parameters: ['true' => '1', 'false' => '0'])])],
        ];
    }

    protected function getRuleHandler(): RuleHandlerInterface
    {
        return new BooleanHandler();
    }
}
