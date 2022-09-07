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
        $defaultError = new Error('The value must be either "1" or "0".');
        $booleanError = new Error('The value must be either "true" or "false".');

        return [
            [$defaultRule, '5', [$defaultError]],

            [$defaultRule, null, [$defaultError]],
            [$defaultRule, [], [$defaultError]],


            [new Boolean(strict: true), true, [$defaultError]],
            [new Boolean(strict: true), false, [$defaultError]],

            [new Boolean(trueValue: true, falseValue: false, strict: true), '0', [$booleanError]],
            [new Boolean(trueValue: true, falseValue: false, strict: true), [], [$booleanError]],
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
            [new Boolean(message: 'Custom error.'), 5, [new Error('Custom error.')]],
        ];
    }

    protected function getRuleHandler(): RuleHandlerInterface
    {
        return new BooleanHandler($this->getTranslator());
    }
}
