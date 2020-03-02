<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\Callback;

class CallbackTest_Object
{
    private function privateMethod($value): Result
    {
        $result = new Result();
        if ($value === 'test') {
            $result->addError('error message');
        }
        return $result;
    }

    private function protectedMethod($value): Result
    {
        $result = new Result();
        if ($value === 'test') {
            $result->addError('error message');
        }
        return $result;
    }

    public function publicMethod($value): Result
    {
        $result = new Result();
        if ($value === 'test') {
            $result->addError('error message');
        }
        return $result;
    }

    public static function staticMethod($value): Result
    {
        $result = new Result();
        if ($value === 'test') {
            $result->addError('error message');
        }
        return $result;
    }

    private static function privateStaticMethod($value): Result
    {
        $result = new Result();
        if ($value === 'test') {
            $result->addError('error message');
        }
        return $result;
    }
}

class CallbackTest extends TestCase
{
    public function testThrowExceptionInvalidArgument()
    {
        foreach ([null, 'test', []] as $value) {
            $this->expectException(\InvalidArgumentException::class);
            new Callback($value);
        }
    }

    public function testValidate(): void
    {
        $rule = new Callback(
            static function ($value): Result {
                $result = new Result();
                if ($value !== 42) {
                    $result->addError('Value should be 42!');
                }
                return $result;
            }
        );

        $result = $rule->validate(41);

        $this->assertFalse($result->isValid());
        $this->assertCount(1, $result->getErrors());
        $this->assertEquals('Value should be 42!', $result->getErrors()[0]);
    }

    public function testValidatePrivateMethod()
    {
        $rule = new Callback([new CallbackTest_Object(), 'privateMethod']);

        $result = $rule->validate('test');
        $this->assertFalse($result->isValid());
        $this->assertCount(1, $result->getErrors());
    }

    public function testValidateProtectedMethod()
    {
        $rule = new Callback([new CallbackTest_Object(), 'protectedMethod']);

        $result = $rule->validate('test');
        $this->assertFalse($result->isValid());
        $this->assertCount(1, $result->getErrors());
    }

    public function testValidatePublicMethod()
    {
        $rule = new Callback([new CallbackTest_Object(), 'publicMethod']);

        $result = $rule->validate('test');
        $this->assertFalse($result->isValid());
        $this->assertCount(1, $result->getErrors());
    }

    public function testThrowExceptionIfNoPassedObject()
    {
        $this->expectException(\InvalidArgumentException::class);
        (new Callback([CallbackTest_Object::class, 'protectedMethod']))->validate(null);

        $this->expectException(\InvalidArgumentException::class);
        (new Callback([CallbackTest_Object::class, 'privateMethod']))->validate(null);

        $this->expectException(\InvalidArgumentException::class);
        (new Callback([CallbackTest_Object::class, 'publicMethod']))->validate(null);
    }

    public function testValidatePublicStaticMethod()
    {
        $rule = new Callback([new CallbackTest_Object(), 'staticMethod']);

        $result = $rule->validate('test');
        $this->assertFalse($result->isValid());
        $this->assertCount(1, $result->getErrors());

        $rule = new Callback([CallbackTest_Object::class, 'staticMethod']);

        $result = $rule->validate('test');
        $this->assertFalse($result->isValid());
        $this->assertCount(1, $result->getErrors());
    }

    public function testValidatePrivateStaticMethod()
    {
        $rule = new Callback([new CallbackTest_Object(), 'privateStaticMethod']);

        $result = $rule->validate('test');
        $this->assertFalse($result->isValid());
        $this->assertCount(1, $result->getErrors());

        $rule = new Callback([CallbackTest_Object::class, 'privateStaticMethod']);

        $result = $rule->validate('test');
        $this->assertFalse($result->isValid());
        $this->assertCount(1, $result->getErrors());
    }
}
