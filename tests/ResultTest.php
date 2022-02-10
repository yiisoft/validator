<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests;

use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\Error;
use Yiisoft\Validator\Result;

class ResultTest extends TestCase
{
    /**
     * @test
     */
    public function isValidByDefault(): void
    {
        $result = new Result();
        $this->assertTrue($result->isValid());
    }

    /**
     * @test
     */
    public function errorsAreEmptyByDefault(): void
    {
        $result = new Result();
        $this->assertEmpty($result->getErrors());
    }

    /**
     * @test
     */
    public function errorIsProperlyAdded(): void
    {
        $result = new Result();
        $result->addError('Error');

        $this->assertContains('Error', $result->getErrors());
    }

    /**
     * @test
     */
    public function addingErrorChangesIsValid(): void
    {
        $result = new Result();
        $result->addError('Error');

        $this->assertFalse($result->isValid());
    }

    public function testGetErrorObjects(): void
    {
        $this->assertEquals(
            [new Error('error1', []), new Error('error2', ['path', 2])],
            $this->createErrorResult()->getErrorObjects()
        );
    }

    public function testGetErrors(): void
    {
        $this->assertSame(['error1', 'error2'], $this->createErrorResult()->getErrors());
    }

    public function testGetErrorsIndexedByPath(): void
    {
        $this->assertEquals(
            ['' => ['error1'], 'path.2' => ['error2']],
            $this->createErrorResult()->getErrorsIndexedByPath()
        );
    }

    private function createErrorResult(): Result
    {
        $result = new Result();
        $result->addError('error1', []);
        $result->addError('error2', ['path', 2]);

        return $result;
    }

    public function testIsAttributeValid(): void
    {
        $result = $this->createAttributeErrorResult();

        $this->assertTrue($result->isAttributeValid('attribute1'));
        $this->assertFalse($result->isAttributeValid('attribute2'));
        $this->assertFalse($result->isAttributeValid(''));
    }

    public function testGetErrorsIndexedByAttribute(): void
    {
        $this->assertEquals(
            ['attribute2' => ['error2.1', 'error2.2', 'error2.3', 'error2.4'], '' => ['error3.1', 'error3.2']],
            $this->createAttributeErrorResult()->getErrorsIndexedByAttribute()
        );
    }

    public function testGetAttributeErrorObjects(): void
    {
        $result = $this->createAttributeErrorResult();

        $this->assertEquals([], $result->getAttributeErrorObjects('attribute1'));
        $this->assertEquals(
            [
                new Error('error2.1', ['attribute2']),
                new Error('error2.2', ['attribute2']),
                new Error('error2.3', ['attribute2', 'nested']),
                new Error('error2.4', ['attribute2', 'nested']),
            ],
            $result->getAttributeErrorObjects('attribute2')
        );
        $this->assertEquals([new Error('error3.1'), new Error('error3.2')], $result->getAttributeErrorObjects(''));
    }

    public function testGetAttributeErrors(): void
    {
        $result = $this->createAttributeErrorResult();

        $this->assertEquals([], $result->getAttributeErrors('attribute1'));
        $this->assertEquals(
            ['error2.1', 'error2.2', 'error2.3', 'error2.4'],
            $result->getAttributeErrors('attribute2')
        );
        $this->assertEquals(['error3.1', 'error3.2'], $result->getAttributeErrors(''));
    }

    public function testGetAttributeErrorsIndexedByPath(): void
    {
        $result = $this->createAttributeErrorResult();

        $this->assertEquals([], $result->getAttributeErrorsIndexedByPath('attribute1'));
        $this->assertEquals(
            ['' => ['error2.1', 'error2.2'], 'nested' => ['error2.3', 'error2.4']],
            $result->getAttributeErrorsIndexedByPath('attribute2')
        );
        $this->assertEquals(['' => ['error3.1', 'error3.2']], $result->getAttributeErrorsIndexedByPath(''));
    }

    private function createAttributeErrorResult(): Result
    {
        $result = new Result();
        $result->addError('error2.1', ['attribute2']);
        $result->addError('error2.2', ['attribute2']);
        $result->addError('error2.3', ['attribute2', 'nested']);
        $result->addError('error2.4', ['attribute2', 'nested']);
        $result->addError('error3.1');
        $result->addError('error3.2');

        return $result;
    }
}
