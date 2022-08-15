<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use Yiisoft\Validator\Error;
use Yiisoft\Validator\Rule\IsTrue;
use Yiisoft\Validator\Rule\IsTrueHandler;
use Yiisoft\Validator\RuleHandlerInterface;

final class IsTrueHandlerTest extends AbstractRuleValidatorTest
{
    public function failedValidationProvider(): array
    {
        $message = 'The value must be "1".';

        return [
            [new IsTrue(), '5', [new Error($message)]],
            [new IsTrue(), null, [new Error($message)]],
            [new IsTrue(), [], [new Error($message)]],
            [new IsTrue(strict: true), true, [new Error($message)]],
            [new IsTrue(trueValue: true, strict: true), '1', [new Error($message)]],
            [new IsTrue(trueValue: true, strict: true), [], [new Error($message)]],

            [new IsTrue(), false, [new Error($message)]],
            [new IsTrue(), '0', [new Error($message)]],
            [new IsTrue(strict: true), '0', [new Error($message)]],
            [new IsTrue(trueValue: true, strict: true), false, [new Error($message)]],
        ];
    }

    public function passedValidationProvider(): array
    {
        return [
            [new IsTrue(), true],
            [new IsTrue(), '1'],
            [new IsTrue(strict: true), '1'],
            [new IsTrue(trueValue: true, strict: true), true],
        ];
    }

    public function customErrorMessagesProvider(): array
    {
        return [
            [
                new IsTrue(message: 'Custom error.'),
                5,
                [
                    new Error('Custom error.'),
                ],
            ],
        ];
    }

    protected function getRuleHandler(): RuleHandlerInterface
    {
        return new IsTrueHandler();
    }
}
