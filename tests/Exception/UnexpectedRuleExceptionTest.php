<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Exception;

use PHPUnit\Framework\TestCase;
use stdClass;
use Yiisoft\Validator\Exception\UnexpectedRuleException;
use Yiisoft\Validator\Rule\Number;

final class UnexpectedRuleExceptionTest extends TestCase
{
    public function testBase(): void
    {
        $exception = new UnexpectedRuleException(Number::class, new stdClass());

        $this->assertSame('Expected "Yiisoft\Validator\Rule\Number", but "stdClass" given.', $exception->getMessage());
        $this->assertSame(0, $exception->getCode());
        $this->assertNull($exception->getPrevious());
    }
}
