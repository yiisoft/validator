<?php


namespace Yiisoft\Validator\Tests;

use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\ResultSet;

class ResultSetTest extends TestCase
{
    /**
     * @test
     */
    public function successShouldNotOverrideError(): void
    {
        $error = (new Result())->addError('error');
        $success = new Result();

        $resultSet = (new ResultSet())
            ->addResult('x', $error)
            ->addResult('x', $success);

        $errors = $resultSet->getResult('x')->getErrors();

        $this->assertFalse($resultSet->getResult('x')->isValid());
        $this->assertCount(1, $errors);
        $this->assertContains('error', $errors);
    }

    /**
     * @test
     */
    public function errorsShouldAdd(): void
    {
        $error1 = (new Result())->addError('error1');
        $error2 = (new Result())->addError('error2');

        $resultSet = (new ResultSet())
            ->addResult('x', $error1)
            ->addResult('x', $error2);

        $errors = $resultSet->getResult('x')->getErrors();

        $this->assertFalse($resultSet->getResult('x')->isValid());
        $this->assertCount(2, $errors);
        $this->assertContains('error1', $errors);
        $this->assertContains('error2', $errors);
    }
}
