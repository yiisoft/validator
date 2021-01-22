<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests;

use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\ErrorMessage;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\ResultSet;

class ResultSetTest extends TestCase
{
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

    public function testGetErrors(): void
    {
        $resultSet = new ResultSet();
        $resultSet->addResult('attribute1', $this->createErrorResult('error1'));
        $resultSet->addResult('attribute1', $this->createErrorResult('error2'));
        $resultSet->addResult('attribute2', new Result());

        $this->assertSame(
            ['attribute1' => ['error1', 'error2']],
            $resultSet->getErrors()
        );
    }

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

    private function createErrorResult(string $error): Result
    {
        $result = new Result();
        $result->addError(new ErrorMessage($error));

        return $result;
    }
}
