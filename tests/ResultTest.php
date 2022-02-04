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
}
