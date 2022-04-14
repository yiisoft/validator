<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\Error;
use Yiisoft\Validator\Exception\InvalidCallbackReturnTypeException;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\Callback;

class CallbackTest extends TestCase
{
    public function testCallback(): void
    {
        $rule1 = new Callback(static function ($value): Result {
            $result = new Result();
            $result->addError('Error 1.');

            return $result;
        });
        $result1 = $rule1->validate('');
        $this->assertEquals(['Error 1.'], $result1->getErrorMessages());

        $rule2 = $rule1->callback(static function ($value): Result {
            $result = new Result();
            $result->addError('Error 2.');

            return $result;
        });
        $result2 = $rule2->validate('');
        $this->assertEquals(['Error 2.'], $result2->getErrorMessages());

        $this->assertNotSame($rule1, $rule2);
    }

    public function testValidate(): void
    {
        $rule = new Callback(static function ($value): Result {
            $result = new Result();
            if ($value !== 42) {
                $result->addError('Value should be 42!');
            }

            return $result;
        });

        $result = $rule->validate(41);

        $this->assertFalse($result->isValid());
        $this->assertEquals(['Value should be 42!'], $result->getErrorMessages());
    }

    public function testThrowExceptionWithInvalidReturn(): void
    {
        $this->expectException(InvalidCallbackReturnTypeException::class);

        $rule = new Callback(static fn (): string => 'invalid return');
        $rule->validate(null);
    }

    public function testGetName(): void
    {
        $rule = new Callback(static fn ($value) => $value);
        $this->assertEquals('callback', $rule->getName());
    }

    public function getOptionsProvider(): array
    {
        return [
            [
                new Callback(static fn ($value) => $value),
                [
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                new Callback(static fn ($value) => $value, skipOnEmpty: true),
                [
                    'skipOnEmpty' => true,
                    'skipOnError' => false,
                ],
            ],
        ];
    }

    /**
     * @dataProvider getOptionsProvider
     */
    public function testGetOptions(Callback $rule, array $expectedOptions): void
    {
        $this->assertEquals($expectedOptions, $rule->getOptions());
    }

    public function testAddErrorWithValuePath(): void
    {
        $rule = new Callback(static function ($value): Result {
            $result = new Result();
            $result->addError('e1', ['key1']);

            return $result;
        });
        $result = $rule->validate('hi');
        $result->addError('e2', ['key2']);

        $this->assertEquals([new Error('e1', ['key1']), new Error('e2', ['key2'])], $result->getErrors());
    }
}
