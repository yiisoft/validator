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
        $message = 'The value must be "{true}".';

        $errors = [new Error($message, parameters: ['true' => 1])];
        $errors1 = [new Error($message, parameters: ['true' => 'true'])];

        return [
            [new IsTrue(), ...$this->createValueAndErrorsPair('5', $errors)],
            [new IsTrue(), ...$this->createValueAndErrorsPair(null, $errors)],
            [new IsTrue(), ...$this->createValueAndErrorsPair([], $errors)],
            [new IsTrue(strict: true), ...$this->createValueAndErrorsPair(true, $errors)],
            [new IsTrue(trueValue: true, strict: true), ...$this->createValueAndErrorsPair('1', $errors1)],
            [new IsTrue(trueValue: true, strict: true), ...$this->createValueAndErrorsPair([], $errors1)],

            [new IsTrue(), ...$this->createValueAndErrorsPair(false, $errors)],
            [new IsTrue(), ...$this->createValueAndErrorsPair('0', $errors)],
            [new IsTrue(strict: true), ...$this->createValueAndErrorsPair('0', $errors)],
            [new IsTrue(trueValue: true, strict: true), ...$this->createValueAndErrorsPair(false, $errors1)],
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
            [new IsTrue(message: 'Custom error.'), ...$this->createValueAndErrorsPair(5, [new Error('Custom error.', parameters: ['true' => 1])])],
        ];
    }

    protected function getRuleHandler(): RuleHandlerInterface
    {
        return new IsTrueHandler();
    }
}
