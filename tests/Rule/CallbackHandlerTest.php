<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use InvalidArgumentException;
use Yiisoft\Validator\Error;
use Yiisoft\Validator\Exception\InvalidCallbackReturnTypeException;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\Callback;
use Yiisoft\Validator\Rule\CallbackHandler;
use Yiisoft\Validator\RuleHandlerInterface;

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
                [new Error('Value should be 42!')],
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
                        $result->addError('Custom error');
                    }

                    return $result;
                }),
                41,
                [new Error('Custom error')],
            ],
        ];
    }

    public function testThrowExceptionWithInvalidReturn(): void
    {
        $rule = new Callback(static fn (): string => 'invalid return');

        $this->expectException(InvalidCallbackReturnTypeException::class);
        $this->validate(null, $rule);
    }

    public function testValidateUsingMethodOutsideAttributeScope(): void
    {
        $rule = new Callback(method: 'validateName');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Using method outside of attribute scope is prohibited.');
        $this->validate(null, $rule);
    }

    protected function getRuleHandler(): RuleHandlerInterface
    {
        return new CallbackHandler($this->getTranslator());
    }
}
