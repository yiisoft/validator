<?php


namespace Yiisoft\Validator\Tests;


use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\Result;

class ResultTest extends TestCase
{
    /**
     * @test
     */
    public function addErrorIsImmutable(): void
    {
        $result1 = $result2 = new Result();
        $result1 = $result1->addError('Error');
        $this->assertNotSame($result1, $result2);
    }

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
        $result = (new Result())->addError('Error');
        $this->assertContains('Error', $result->getErrors());
    }

    /**
     * @test
     */
    public function addingErrorChangesIsValid(): void
    {
        $result = (new Result())->addError('Error');
        $this->assertFalse($result->isValid());
    }
}
