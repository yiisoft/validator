<?php


namespace Yiisoft\Validator\Tests;

use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\ResultSet;

class ResultTest extends TestCase
{
    public function testSetErrorAndSuccess(): void
    {
        $resultSet = new ResultSet();
        $result = new Result();
        $result->addError('test');
        $resultSet->addResult('x', $result);
        $resultSet->addResult('x', new Result());
        $this->assertFalse($resultSet->getResult('x')->isValid());
    }
}
