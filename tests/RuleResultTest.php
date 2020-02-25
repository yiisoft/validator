<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests;

use Yiisoft\Validator\Result;
use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\RuleResult;

class RuleResultTest extends TestCase
{
    /**
     * @test
     */
    public function isValidByDefault(): void
    {
        $result = new RuleResult();
        $this->assertTrue($result->isValid());
    }

    /**
     * @test
     */
    public function errorsAreEmptyByDefault(): void
    {
        $result = new RuleResult();
        $this->assertEmpty($result->getErrors());
    }

    /**
     * @test
     */
    public function errorIsProperlyAdded(): void
    {
        $result = new RuleResult();
        $result->addError('Error');
        $this->assertEquals([['Error', []]], $result->getErrors());
    }

    public function errorIsProperlyAddedWithArguments(): void
    {
        $result = new RuleResult();
        $result->addError('Error', ['value' => 'test']);
        $this->assertEquals([['Error', ['value' => 'test']]], $result->getErrors());
    }

    /**
     * @test
     */
    public function addingErrorChangesIsValid(): void
    {
        $result = new Result();
        $result->addError('Error');
        $this->assertFalse($result->isValid());
    }
}
