<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\Error;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\Callback;
use Yiisoft\Validator\Rule\Nested;
use Yiisoft\Validator\Validator;

class ResultTest extends TestCase
{
    public function isValidByDefault(): void
    {
        $result = new Result();
        $this->assertTrue($result->isValid());
    }

    public function errorsAreEmptyByDefault(): void
    {
        $result = new Result();
        $this->assertEmpty($result->getErrorMessages());
    }

    public function errorsAreProperlyAdded(): void
    {
        $result = new Result();
        $result->addError('Error 1')
            ->addError('Error 2');

        $this->assertEquals(['Error 1', 'Error 2'], $result->getErrorMessages());
    }

    public function addingErrorChangesIsValid(): void
    {
        $result = new Result();
        $result->addError('Error');

        $this->assertFalse($result->isValid());
    }

    public function testGetErrors(): void
    {
        $this->assertEquals(
            [new Error('error1'), new Error('error2', [], ['path', 2])],
            $this->createErrorResult()->getErrors()
        );
    }

    public function testGetErrorMessages(): void
    {
        $this->assertSame(['error1', 'error2'], $this->createErrorResult()->getErrorMessages());
    }

    public function testGetErrorMessagesIndexedByPath(): void
    {
        $this->assertEquals(
            ['' => ['error1'], 'path.2' => ['error2']],
            $this->createErrorResult()->getErrorMessagesIndexedByPath()
        );
    }

    public function testGetErrorMessagesIndexedByPathWithAttributes(): void
    {
        $this->assertEquals(
            [
                'attribute2' => ['error2.1', 'error2.2'],
                'attribute2.nested' => ['error2.3', 'error2.4'],
                '' => ['error3.1', 'error3.2'],
                'attribute4.subattribute4\.1.subattribute4*2' => ['error4.1'],
                'attribute4.subattribute4\.3.subattribute4*4' => ['error4.2'],
            ],
            $this->createAttributeErrorResult()->getErrorMessagesIndexedByPath()
        );
    }

    private function createErrorResult(): Result
    {
        $result = new Result();
        $result
            ->addError('error1')
            ->addError('error2', [], ['path', 2]);

        return $result;
    }

    public function testIsAttributeValid(): void
    {
        $result = $this->createAttributeErrorResult();

        $this->assertTrue($result->isAttributeValid('attribute1'));
        $this->assertFalse($result->isAttributeValid('attribute2'));
        $this->assertFalse($result->isAttributeValid('attribute4'));
    }

    public function testGetErrorMessagesIndexedByAttribute(): void
    {
        $this->assertEquals(
            [
                'attribute2' => ['error2.1', 'error2.2', 'error2.3', 'error2.4'],
                '' => ['error3.1', 'error3.2'],
                'attribute4' => ['error4.1', 'error4.2'],
            ],
            $this->createAttributeErrorResult()->getErrorMessagesIndexedByAttribute()
        );
    }

    public function testGetErrorMessagesIndexedByAttribute_IncorrectType(): void
    {
        $result = new Result();

        $result->addError('error1', [], [1]);

        $this->expectException(InvalidArgumentException::class);
        $result->getErrorMessagesIndexedByAttribute();
    }

    public function testGetAttributeErrors(): void
    {
        $result = $this->createAttributeErrorResult();

        $this->assertEquals([], $result->getAttributeErrors('attribute1'));
        $this->assertEquals(
            [
                new Error('error2.1', [], ['attribute2']),
                new Error('error2.2', [], ['attribute2']),
                new Error('error2.3', [], ['attribute2', 'nested']),
                new Error('error2.4', [], ['attribute2', 'nested']),
            ],
            $result->getAttributeErrors('attribute2')
        );
        $this->assertEquals([new Error('error3.1'), new Error('error3.2')], $result->getAttributeErrors(''));
        $this->assertEquals(
            [
                new Error('error4.1', [], ['attribute4', 'subattribute4.1', 'subattribute4*2']),
                new Error('error4.2', [], ['attribute4', 'subattribute4.3', 'subattribute4*4']),
            ],
            $result->getAttributeErrors('attribute4')
        );
    }

    public function testGetAttributeErrorMessages(): void
    {
        $result = $this->createAttributeErrorResult();

        $this->assertEquals([], $result->getAttributeErrorMessages('attribute1'));
        $this->assertEquals(
            ['error2.1', 'error2.2', 'error2.3', 'error2.4'],
            $result->getAttributeErrorMessages('attribute2')
        );
        $this->assertEquals(['error3.1', 'error3.2'], $result->getAttributeErrorMessages(''));
        $this->assertEquals(['error4.1', 'error4.2'], $result->getAttributeErrorMessages('attribute4'));
    }

    public function testGetAttributeErrorMessagesIndexedByPath(): void
    {
        $result = $this->createAttributeErrorResult();

        $this->assertEquals([], $result->getAttributeErrorMessagesIndexedByPath('attribute1'));
        $this->assertEquals(
            ['' => ['error2.1', 'error2.2'], 'nested' => ['error2.3', 'error2.4']],
            $result->getAttributeErrorMessagesIndexedByPath('attribute2')
        );
        $this->assertEquals(['' => ['error3.1', 'error3.2']], $result->getAttributeErrorMessagesIndexedByPath(''));
        $this->assertEquals([
            'subattribute4\.1.subattribute4*2' => ['error4.1'],
            'subattribute4\.3.subattribute4*4' => ['error4.2'],
        ], $result->getAttributeErrorMessagesIndexedByPath('attribute4'));
    }

    public function testGetCommonErrorMessages(): void
    {
        $this->assertEquals(['error3.1', 'error3.2'], $this->createAttributeErrorResult()->getCommonErrorMessages());
    }

    /**
     * @see https://github.com/yiisoft/validator/issues/610
     */
    public function testDataKeysWithDots(): void
    {
        $result = (new Validator())->validate(
            [
                'user.age' => 17,
                'meta' => [
                    'tag' => 'hi',
                ],
            ],
            [
                'user.age' => static fn() => (new Result())->addError('Too young.'),
                'meta' => new Nested([
                    'tag' => new Callback(static fn() => (new Result())->addError('Too short.')),
                ]),
            ],
        );

        $this->assertSame(
            [
                'user\.age' => ['Too young.'],
                'meta.tag' => ['Too short.'],
            ],
            $result->getErrorMessagesIndexedByPath()
        );
    }

    public function testEscapeInGetErrorMessagesIndexedByPath(): void
    {
        $result = (new Result())->addError('e1', valuePath: ['user', 'meta.the-age']);

        $this->assertSame(
            [
                'user.meta.the\-age' => ['e1'],
            ],
            $result->getErrorMessagesIndexedByPath(escape: '-'),
        );
    }

    public function testEscapeInGetAttributeErrorMessagesIndexedByPath(): void
    {
        $result = (new Result())->addError('e1', valuePath: ['user', 'data', 'meta.the-age']);

        $this->assertSame(
            [
                'data.meta.the\-age' => ['e1'],
            ],
            $result->getAttributeErrorMessagesIndexedByPath('user', escape: '-'),
        );
    }

    private function createAttributeErrorResult(): Result
    {
        $result = new Result();
        $result
            ->addError('error2.1', [], ['attribute2'])
            ->addError('error2.2', [], ['attribute2'])
            ->addError('error2.3', [], ['attribute2', 'nested'])
            ->addError('error2.4', [], ['attribute2', 'nested'])
            ->addError('error3.1')
            ->addError('error3.2')
            ->addError('error4.1', [], ['attribute4', 'subattribute4.1', 'subattribute4*2'])
            ->addError('error4.2', [], ['attribute4', 'subattribute4.3', 'subattribute4*4']);

        return $result;
    }
}
