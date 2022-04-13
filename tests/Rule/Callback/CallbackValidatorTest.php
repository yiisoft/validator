<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule\Callback;

use Yiisoft\Validator\Error;
use Yiisoft\Validator\Exception\InvalidCallbackReturnTypeException;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\Callback\Callback;
use Yiisoft\Validator\Rule\Callback\CallbackValidator;
use Yiisoft\Validator\Rule\RuleValidatorInterface;
use Yiisoft\Validator\Tests\Rule\AbstractRuleValidatorTest;

/**
 * @group t
 */
final class CallbackValidatorTest extends AbstractRuleValidatorTest
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
        ];
    }

    public function testThrowExceptionWithInvalidReturn(): void
    {
        $this->expectException(InvalidCallbackReturnTypeException::class);

        $rule = new Callback(static fn(): string => 'invalid return');

        $this->validate(null, $rule);
    }

    public function testAddErrorWithValuePath(): void
    {
        $this->markTestIncomplete('Add value path feature');
        $rule = new Callback(static function ($value): Result {
            $result = new Result();
            $result->addError('e1', ['key1']);

            return $result;
        });
        $result = $this->validate('hi', $rule);

        $result->addError('e2', ['key2']);

        $this->assertEquals([new Error('e1', ['key1']), new Error('e2', ['key2'])], $result->getErrors());
    }

    protected function getValidator(): RuleValidatorInterface
    {
        return new CallbackValidator();
    }

    protected function getConfigClassName(): string
    {
        return Callback::class;
    }
}
