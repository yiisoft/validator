<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use InvalidArgumentException;
use RuntimeException;
use stdClass;
use Yiisoft\Validator\DataSet\ObjectDataSet;
use Yiisoft\Validator\Exception\InvalidCallbackReturnTypeException;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\Callback;
use Yiisoft\Validator\Rule\CallbackHandler;
use Yiisoft\Validator\RuleInterface;
use Yiisoft\Validator\Tests\Rule\Base\DifferentRuleInHandlerTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\RuleTestCase;
use Yiisoft\Validator\Tests\Rule\Base\RuleWithOptionsTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\SkipOnErrorTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\WhenTestTrait;
use Yiisoft\Validator\Tests\Support\ValidatorFactory;
use Yiisoft\Validator\ValidationContext;

final class CallbackTest extends RuleTestCase
{
    use DifferentRuleInHandlerTestTrait;
    use RuleWithOptionsTestTrait;
    use SkipOnErrorTestTrait;
    use WhenTestTrait;

    public function testInitWithNoCallbackAndMethodException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Either "$callback" or "$method" must be specified.');
        new Callback();
    }

    public function testInitWithBothCallbackAndMethodException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('"$callback" and "$method" are mutually exclusive.');
        new Callback(callback: static fn (): Result => new Result(), method: 'test');
    }

    public function testGetName(): void
    {
        $rule = new Callback(callback: static fn (): Result => new Result());
        $this->assertSame('callback', $rule->getName());
    }

    public function testGetMethod(): void
    {
        $rule = new Callback(method: 'test');
        $this->assertSame('test', $rule->getMethod());
    }

    public function testAfterInitAttributeWithNoMethod(): void
    {
        $rule = new Callback(callback: static fn (): Result => new Result());
        $callback = $rule->getCallback();
        $this->assertIsCallable($callback);
        $this->assertNull($rule->getMethod());

        $rule->afterInitAttribute(new ObjectDataSet(new stdClass()));
        $this->assertIsCallable($callback);
        $this->assertNull($rule->getMethod());
        $this->assertSame($callback, $rule->getCallback());
    }

    public function dataOptions(): array
    {
        return [
            [
                new Callback(
                    static fn (mixed $value, object $rule, ValidationContext $context): Result => new Result(),
                ),
                [
                    'method' => null,
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                new Callback(
                    static fn (mixed $value, object $rule, ValidationContext $context): Result => new Result(),
                    skipOnEmpty: true,
                ),
                [
                    'method' => null,
                    'skipOnEmpty' => true,
                    'skipOnError' => false,
                ],
            ],
            [
                new Callback(method: 'test'),
                [
                    'method' => 'test',
                    'skipOnEmpty' => false,
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
            'non-static callable' => [
                new class (1) {
                    public function __construct(
                        #[Callback(method: 'validateName')]
                        #[Callback(method: 'staticValidateName')]
                        private $age,
                    ) {
                    }

                    private function validateName(mixed $value, RuleInterface $rule, ValidationContext $context): Result
                    {
                        if ($value !== $this->age) {
                            throw new RuntimeException('Method scope was not bound to the object.');
                        }

                        $result = new Result();
                        $result->addError('Hello from non-static method.');

                        return $result;
                    }

                    private static function staticValidateName(
                        mixed $value,
                        RuleInterface $rule,
                        ValidationContext $context
                    ): Result {
                        if ($value !== $context->getDataSet()->getAttributeValue('age')) {
                            throw new RuntimeException('Method scope was not bound to the object.');
                        }

                        $result = new Result();
                        $result->addError('Hello from static method.');

                        return $result;
                    }
                },
                null,
                ['age' => ['Hello from non-static method.', 'Hello from static method.']],
            ],
        ];
    }

    public function testThrowExceptionWithInvalidReturn(): void
    {
        $callback = static fn (mixed $value, object $rule, ValidationContext $context): string => 'invalid return';
        $rule = new Callback($callback);
        $validator = ValidatorFactory::make();

        $this->expectException(InvalidCallbackReturnTypeException::class);
        $message = 'Return value of callback must be an instance of Yiisoft\Validator\Result, string returned.';
        $this->expectExceptionMessage($message);
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

    public function testSkipOnError(): void
    {
        $this->testSkipOnErrorInternal(
            new Callback(callback: static fn (): Result => new Result()),
            new Callback(callback: static fn (): Result => new Result(), skipOnError: true),
        );
    }

    public function testWhen(): void
    {
        $when = static fn (mixed $value): bool => $value !== null;
        $this->testWhenInternal(
            new Callback(callback: static fn (): Result => new Result()),
            new Callback(callback: static fn (): Result => new Result(), when: $when),
        );
    }

    protected function getDifferentRuleInHandlerItems(): array
    {
        return [Callback::class, CallbackHandler::class];
    }
}
