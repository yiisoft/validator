<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests;

use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\Error;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\ResultSet;

class ResultSetTest extends TestCase
{
    public function testIsValidReturnTrue(): void
    {
        $resultSet = new ResultSet();
        $resultSet->addResult('attribute1', new Result());

        $this->assertTrue($resultSet->isValid());
    }

    public function testIsValidReturnFalse(): void
    {
        $resultSet = new ResultSet();
        $resultSet->addResult('attribute1', $this->createErrorResult('error1'));

        $this->assertFalse($resultSet->isValid());
    }

    public function testSuccessShouldNotOverrideError(): void
    {
        $error = $this->createErrorResult('error');
        $success = new Result();

        $resultSet = new ResultSet();
        $resultSet->addResult('x', $error);
        $resultSet->addResult('x', $success);

        $errors = $resultSet->getResult('x')->getErrors();

        $this->assertFalse($resultSet->getResult('x')->isValid());
        $this->assertCount(1, $errors);
        $this->assertContains('error', $errors);
    }

    public function testErrorsShouldAdd(): void
    {
        $resultSet = new ResultSet();
        $resultSet->addResult('x', $this->createErrorResult('error1'));
        $resultSet->addResult('x', $this->createErrorResult('error2'));

        $errors = $resultSet->getResult('x')->getErrors();

        $this->assertFalse($resultSet->getResult('x')->isValid());
        $this->assertCount(2, $errors);
        $this->assertContains('error1', $errors);
        $this->assertContains('error2', $errors);
    }

    public function testGetErrorObjects(): void
    {
        $this->assertEquals(
            [
                'attribute1' => [new Error('error1', []), new Error('error2', [])],
                'attribute2' => [new Error('error3', ['path', 3])],
            ],
            $this->createErrorResultSet()->getErrorObjects()
        );
    }

    public function testGetErrors(): void
    {
        $this->assertSame(
            ['attribute1' => ['error1', 'error2'], 'attribute2' => ['error3']],
            $this->createErrorResultSet()->getErrors()
        );
    }

    public function testGetNestedErrors(): void
    {
        $this->assertEquals(
            ['attribute1' => [0 => ['error1', 'error2']], 'attribute2' => ['path' => [3 => ['error3']]]],
            $this->createErrorResultSet()->getNestedErrors()
        );
    }

    public function testGetErrorsIndexedByPath_Empty(): void
    {
        $this->assertEquals(
            ['attribute1' => ['' => ['error1', 'error2']], 'attribute2' => ['path.3' => ['error3']]],
            $this->createErrorResultSet()->getErrorsIndexedByPath()
        );
    }

    private function createErrorResult(string $error, array $valuePath = []): Result
    {
        $result = new Result();
        $result->addError($error, $valuePath);

        return $result;
    }

    private function createErrorResultSet(): ResultSet
    {
        $resultSet = new ResultSet();
        $resultSet->addResult('attribute1', $this->createErrorResult('error1'));
        $resultSet->addResult('attribute1', $this->createErrorResult('error2'));
        $resultSet->addResult('attribute2', $this->createErrorResult('error3', ['path', 3]));
        $resultSet->addResult('attribute3', new Result());

        return $resultSet;
    }
}
