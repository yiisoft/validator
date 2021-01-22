<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests;

use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\ErrorMessage;
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
        $result->addError(new ErrorMessage('Error'));
        $this->assertSame(['Error'], $result->getErrors());
    }

    /**
     * @test
     */
    public function errorIsProperlyAddedWithFormatter(): void
    {
        $result = new Result();
        $result->addError(new ErrorMessage('Error'));
        $formatter = (new FormatterMockFactory())->create();
        $this->assertEquals(['Translate: Error'], $result->getErrors($formatter));
    }

    /**
     * @test
     */
    public function addingErrorChangesIsValid(): void
    {
        $result = new Result();
        $result->addError(new ErrorMessage('Error'));
        $this->assertFalse($result->isValid());
    }

//    public function testAddResult(): void
//    {
//
//    }
}
