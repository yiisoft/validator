<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use Yiisoft\Validator\Error;
use Yiisoft\Validator\Exception\InvalidCallbackReturnTypeException;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\Callback;
use Yiisoft\Validator\Rule\CallbackHandler;
use Yiisoft\Validator\Rule\RuleHandlerInterface;

final class CallbackHandlerTest extends AbstractRuleValidatorTest
{
    public function failedValidationProvider(): array
    {
        return [
            [
                new Callback(static function ($value): Result {
                    $result = new Result();
                    if ($value !== 42) {
                        $result->addError('Value should be 42!');
                    }

                    return $result;
                }),
                41,
                [new Error('Value should be 42!', [])],
            ],
        ];
    }

    public function passedValidationProvider(): array
    {
        return [
            [
                new Callback(static function ($value): Result {
                    $result = new Result();
                    if ($value !== 42) {
                        $result->addError('Value should be 42!');
                    }

                    return $result;
                }),
                42,
            ],
        ];
    }

    public function customErrorMessagesProvider(): array
    {
        return [
            [
                new Callback(static function ($value): Result {
                    $result = new Result();
                    if ($value !== 42) {
                        $result->addError('Custom error', [], 'attribute name');
                    }

                    return $result;
                }),
                41,
                [new Error('Custom error', [], 'attribute name')],
            ],
        ];
    }

    public function testThrowExceptionWithInvalidReturn(): void
    {
        $this->expectException(InvalidCallbackReturnTypeException::class);

        $rule = new Callback(static fn (): string => 'invalid return');

        $this->validate(null, $rule);
    }

    protected function getValidator(): RuleHandlerInterface
    {
        return new CallbackHandler();
    }
}
