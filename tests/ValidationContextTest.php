<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests;

use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\Tests\Stub\DataSet;
use Yiisoft\Validator\ValidationContext;

final class ValidationContextTest extends TestCase
{
    public function testDataSet(): void
    {
        $context = new ValidationContext();
        $this->assertNull($context->getDataSet());

        $dataSet = new DataSet();
        $context = new ValidationContext($dataSet);
        $this->assertSame($dataSet, $context->getDataSet());
    }

    public function testAttribute(): void
    {
        $context = new ValidationContext();

        $this->assertNull($context->getAttribute());

        $context = $context->withAttribute('key');
        $this->assertSame('key', $context->getAttribute());
    }

    public function testParams(): void
    {
        $context = new ValidationContext();

        $this->assertSame([], $context->getParams());

        $context = $context->withParams(['key' => 42]);
        $this->assertSame(['key' => 42], $context->getParams());
    }

    public function testPreviousRulesErrored(): void
    {
        $context = new ValidationContext();

        $this->assertFalse($context->isPreviousRulesErrored());

        $context = $context->withPreviousRulesErrored(true);
        $this->assertTrue($context->isPreviousRulesErrored());
    }

    public function testImmutability(): void
    {
        $context = new ValidationContext();
        $this->assertNotSame($context, $context->withAttribute(null));
        $this->assertNotSame($context, $context->withParams([]));
        $this->assertNotSame($context, $context->withPreviousRulesErrored(false));
    }
}
