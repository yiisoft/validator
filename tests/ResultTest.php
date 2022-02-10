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
    }

    public function testGetAttributeErrorObjects(): void
    {
        $this->assertEquals([], $this->createAttributeErrorResult()->getAttributeErrorObjects('attribute1'));
        $this->assertEquals(
            [
                new Error('error2.1', ['attribute2']),
                new Error('error2.2', ['attribute2']),
                new Error('error2.3', ['attribute2', 'nested']),
                new Error('error2.4', ['attribute2', 'nested']),
            ],
            $this->createAttributeErrorResult()->getAttributeErrorObjects('attribute2')
        );
    }

    public function testGetTopLevelAttributeErrors(): void
    {
        $this->assertEquals(
            ['attribute2' => ['error2.1', 'error2.2', 'error2.3', 'error2.4']],
            $this->createAttributeErrorResult()->getTopLevelAttributeErrors()
        );
    }

    public function testGetAttributeErrors(): void
    {
        $this->assertEquals([], $this->createAttributeErrorResult()->getAttributeErrors('attribute1'));
        $this->assertEquals(
            ['error2.1', 'error2.2', 'error2.3', 'error2.4'],
            $this->createAttributeErrorResult()->getAttributeErrors('attribute2')
        );
    }

    public function testGetAttributeErrorsIndexedByPath(): void
    {
        $this->assertEquals([], $this->createAttributeErrorResult()->getAttributeErrorsIndexedByPath('attribute1'));
        $this->assertEquals(
            ['' => ['error2.1', 'error2.2'], 'nested' => ['error2.3', 'error2.4']],
            $this->createAttributeErrorResult()->getAttributeErrorsIndexedByPath('attribute2')
        );
    }

    private function createAttributeErrorResult(): Result
    {
        $result = new Result();
        $result->addError('error2.1', ['attribute2']);
        $result->addError('error2.2', ['attribute2']);
        $result->addError('error2.3', ['attribute2', 'nested']);
        $result->addError('error2.4', ['attribute2', 'nested']);

        return $result;
    }
}
