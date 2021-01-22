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

    public function testAddResult(): void
    {
        $result = new Result();
        $result->addError(new ErrorMessage('Error 1.1'));
        $result->addError(new ErrorMessage('Error 1.2'));
        $this->assertCount(2, $result->getErrors());

        $result2 = new Result();
        $result2->addError(new ErrorMessage('Error 2.1'));
        $result2->addError(new ErrorMessage('Error 2.2'));
        $this->assertCount(2, $result2->getErrors());

        $result->addResult($result2);

        $this->assertCount(2, $result2->getErrors());
        $this->assertCount(4, $result->getErrors());

        $this->assertSame(
            [
                'Error 1.1',
                'Error 1.2',
                'Error 2.1',
                'Error 2.2',
            ],
            $result->getErrors()
        );
    }
}
