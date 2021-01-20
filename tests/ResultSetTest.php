<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests;

use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\ErrorMessage;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\ResultSet;

class ResultSetTest extends TestCase
{
    /**
     * @test
     */
    public function successShouldNotOverrideError(): void
    {
        $error = new Result();
        $error->addError(new ErrorMessage('error'));
        $success = new Result();

        $resultSet = new ResultSet();
        $resultSet->addResult('x', $error);
        $resultSet->addResult('x', $success);

        $errors = $resultSet->getResult('x')->getErrors();

        $this->assertFalse($resultSet->getResult('x')->isValid());
        $this->assertCount(1, $errors);
        $this->assertEquals([new ErrorMessage('error')], $errors);
    }

    /**
     * @test
     */
    public function errorsShouldAdd(): void
    {
        $error1 = new Result();
        $error1->addError(new ErrorMessage('error1'));

        $error2 = new Result();
        $error2->addError(new ErrorMessage('error2'));

        $resultSet = new ResultSet();
        $resultSet->addResult('x', $error1);
        $resultSet->addResult('x', $error2);

        $errors = $resultSet->getResult('x')->getErrors();

        $this->assertFalse($resultSet->getResult('x')->isValid());
        $this->assertCount(2, $errors);
        $this->assertSame([
            'error1',
            'error2',
        ], $errors);

        $errorsRaw = $resultSet->getResult('x')->getRawErrors();
        $this->assertContainsOnlyInstancesOf(ErrorMessage::class, $errorsRaw);
        $this->assertEquals([
            new ErrorMessage('error1'),
            new ErrorMessage('error2'),
        ], $errorsRaw);
    }
}
