<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests;

use Yiisoft\Validator\ErrorMessage;
use Yiisoft\Validator\Result;

class ResultTest extends FormatterMock
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
        $this->assertEquals([new ErrorMessage('Error')], $result->getErrors());
    }

    /**
     * @test
     */
    public function errorIsProperlyAddedWithFormatter(): void
    {
        $result = new Result();
        $result->addError(new ErrorMessage('Error'));
        $this->assertEquals(['Translate: Error'], $result->getErrors($this->createFormatterMock()));
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
}
