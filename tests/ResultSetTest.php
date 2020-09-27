<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests;

use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\Error;
use Yiisoft\Validator\Errors;

class ResultSetTest extends TestCase
{
    /**
     * @test
     */
    public function successShouldNotOverrideError(): void
    {
        $error = new Error();
        $error->addError('error');
        $success = new Error();

        $resultSet = new Errors();
        $resultSet->addResult('x', $error);
        $resultSet->addResult('x', $success);

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
        $error1 = new Error();
        $error1->addError('error1');

        $error2 = new Error();
        $error2->addError('error2');

        $resultSet = new Errors();
        $resultSet->addResult('x', $error1);
        $resultSet->addResult('x', $error2);

        $errors = $resultSet->getResult('x')->getErrors();

        $this->assertFalse($resultSet->getResult('x')->isValid());
        $this->assertCount(2, $errors);
        $this->assertContains('error1', $errors);
        $this->assertContains('error2', $errors);
    }
}
