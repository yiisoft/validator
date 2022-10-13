<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use InvalidArgumentException;
use Yiisoft\Validator\Exception\InvalidCallbackReturnTypeException;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\Callback;
use Yiisoft\Validator\Rule\CallbackHandler;
use Yiisoft\Validator\Tests\Rule\Base\DifferentRuleInHandlerTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\RuleTestCase;
use Yiisoft\Validator\Tests\Rule\Base\SerializableRuleTestTrait;
use Yiisoft\Validator\Tests\Support\ValidatorFactory;
use Yiisoft\Validator\ValidationContext;

final class CallbackTest extends RuleTestCase
{
    use DifferentRuleInHandlerTestTrait;
    use SerializableRuleTestTrait;

    public function testGetName(): void
    {
        $rule = new Callback(callback: fn () => new Result());
        $this->assertSame('callback', $rule->getName());
    }

    public function dataOptions(): array
    {
        return [
            [
                new Callback(
                    static fn (mixed $value, object $rule, ValidationContext $context): Result => new Result()
                ),
                [
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                new Callback(
                    static fn (mixed $value, object $rule, ValidationContext $context): Result => new Result(),
                    skipOnEmpty: true
                ),
                [
                    'skipOnEmpty' => true,
                    'skipOnError' => false,
                ],
            ],
        ];
    }

    public function dataValidationPassed(): array
    {
        return [
            [
                42,
                [
                    new Callback(static function (mixed $value, object $rule, ValidationContext $context): Result {
                        $result = new Result();
                        if ($value !== 42) {
                            $result->addError('Value should be 42!');
                        }

                        return $result;
                    }),
                ],
            ],
        ];
    }

    public function dataValidationFailed(): array
    {
        return [
            [
                41,
                [
                    new Callback(static function (mixed $value, object $rule, ValidationContext $context): Result {
                        $result = new Result();
                        if ($value !== 42) {
                            $result->addError('Value should be 42!');
                        }

                        return $result;
                    }),
                ],
                ['' => ['Value should be 42!']],
            ],
            'custom error' => [
                41,
                [
                    new Callback(static function (mixed $value, object $rule, ValidationContext $context): Result {
                        $result = new Result();
                        if ($value !== 42) {
                            $result->addError('Custom error');
                        }

                        return $result;
                    }),
                ],
                ['' => ['Custom error']],
            ],
        ];
    }

    public function testThrowExceptionWithInvalidReturn(): void
    {
        $rule = new Callback(
            static fn (mixed $value, object $rule, ValidationContext $context): string => 'invalid return'
        );
        $validator = ValidatorFactory::make();

        $this->expectException(InvalidCallbackReturnTypeException::class);
        $validator->validate(null, [$rule]);
    }

    public function testValidateUsingMethodOutsideAttributeScope(): void
    {
        $rule = new Callback(method: 'validateName');
        $validator = ValidatorFactory::make();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Using method outside of attribute scope is prohibited.');
        $validator->validate(null, [$rule]);
    }

    protected function getDifferentRuleInHandlerItems(): array
    {
        return [Callback::class, CallbackHandler::class];
    }
}
