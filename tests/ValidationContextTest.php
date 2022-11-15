<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests;

use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\DataSet\ArrayDataSet;
use Yiisoft\Validator\DataSet\SingleValueDataSet;
use Yiisoft\Validator\Tests\Support\ValidatorFactory;
use Yiisoft\Validator\ValidationContext;

final class ValidationContextTest extends TestCase
{
    public function testDefault(): void
    {
        $dataSet = new SingleValueDataSet(null);
        $context = new ValidationContext(ValidatorFactory::make(), $dataSet);
        $this->assertSame($dataSet, $context->getDataSet());
        $this->assertNull($context->getAttribute());
        $this->assertSame([], $context->getParameters());
    }

    public function testConstructor(): void
    {
        $dataSet = new ArrayDataSet();

        $context = new ValidationContext(ValidatorFactory::make(), $dataSet, 'name', ['key' => 42]);

        $this->assertSame($dataSet, $context->getDataSet());
        $this->assertSame('name', $context->getAttribute());
        $this->assertSame(['key' => 42], $context->getParameters());
    }

    public function testSetParameter(): void
    {
        $context = new ValidationContext(ValidatorFactory::make(), new SingleValueDataSet(null));
        $context->setParameter('key', 42);

        $this->assertSame(['key' => 42], $context->getParameters());
    }

    public function testGetParameter(): void
    {
        $context = new ValidationContext(ValidatorFactory::make(), new SingleValueDataSet(null), null, ['key' => 42]);

        $this->assertSame(42, $context->getParameter('key'));
        $this->assertNull($context->getParameter('non-exists'));
        $this->assertSame(7, $context->getParameter('non-exists', 7));
    }
}
