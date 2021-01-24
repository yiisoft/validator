<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests;

use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\Tests\Stub\DataSet;
use Yiisoft\Validator\ValidationContext;

final class ValidationContextTest extends TestCase
{
    public function testDefault(): void
    {
        $context = new ValidationContext();
        $this->assertNull($context->getDataSet());
        $this->assertNull($context->getAttribute());
        $this->assertSame([], $context->getParams());
    }

    public function testConstructor(): void
    {
        $dataSet = new DataSet();

        $context = new ValidationContext($dataSet, 'name', ['key' => 42]);

        $this->assertSame($dataSet, $context->getDataSet());
        $this->assertSame('name', $context->getAttribute());
        $this->assertSame(['key' => 42], $context->getParams());
    }

    public function testWithAttribute(): void
    {
        $context = new ValidationContext(null, 'key');

        $newContext = $context->withAttribute('newKey');

        $this->assertNotSame($context, $newContext);
        $this->assertSame('key', $context->getAttribute());
        $this->assertSame('newKey', $newContext->getAttribute());
    }

    public function testSetParam(): void
    {
        $context = new ValidationContext();
        $context->setParam('key', 42);
        $this->assertSame(['key' => 42], $context->getParams());
    }

    public function testGetParam(): void
    {
        $context = new ValidationContext(null, null, ['key' => 42]);

        $this->assertSame(42, $context->getParam('key'));
        $this->assertSame(null, $context->getParam('non-exists'));
        $this->assertSame(7, $context->getParam('non-exists', 7));
    }
}
