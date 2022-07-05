<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule\SelfValidatable;

use Yiisoft\Validator\Error;
use Yiisoft\Validator\Exception\InvalidCallbackReturnTypeException;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\SelfValidatableRuleInterface;
use Yiisoft\Validator\Tests\Stub\CallbackSelfValidatableRule;

final class CallbackSelfValidatableTest extends AbstractSelfValidatableRuleTest
{
    public function failedValidationProvider(): array
    {
        return [
            [
                new CallbackSelfValidatableRule(static function ($value): Result {
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
                new CallbackSelfValidatableRule(static function ($value): Result {
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
                new CallbackSelfValidatableRule(static function ($value): Result {
                    $result = new Result();
                    if ($value !== 42) {
                        $result->addError('Custom error', []);
                    }

                    return $result;
                }),
                41,
                [new Error('Custom error', [])],
            ],
        ];
    }

    public function testThrowExceptionWithInvalidReturn(): void
    {
        $this->expectException(InvalidCallbackReturnTypeException::class);

        $rule = new CallbackSelfValidatableRule(static fn (): string => 'invalid return');

        $rule->validate(null);
    }

    public function optionsDataProvider(): array
    {
        return [
            [
                new CallbackSelfValidatableRule(static fn ($value) => $value),
                [
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                new CallbackSelfValidatableRule(static fn ($value) => $value, skipOnEmpty: true),
                [
                    'skipOnEmpty' => true,
                    'skipOnError' => false,
                ],
            ],
        ];
    }

    protected function getRule(): SelfValidatableRuleInterface
    {
        return new CallbackSelfValidatableRule([]);
    }
}
