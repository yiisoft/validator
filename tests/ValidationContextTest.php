<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests;

use PHPUnit\Framework\TestCase;
use RuntimeException;
use Yiisoft\Validator\DataSet\ArrayDataSet;
use Yiisoft\Validator\ValidationContext;
use Yiisoft\Validator\Validator;

final class ValidationContextTest extends TestCase
{
    public function testDefault(): void
    {
        $context = new ValidationContext(new Validator(), 7);

        $this->assertSame(7, $context->getRawData());
        $this->assertNull($context->getAttribute());
    }

    public function testGetDataSetWithoutDataSet(): void
    {
        $context = new ValidationContext(new Validator(), 7);

        $this->expectException(RuntimeException::class);
        $this->expectErrorMessage('Data set in validation context is not set.');
        $context->getDataSet();
    }

    public function testConstructor(): void
    {
        $data = ['x' => 7];

        $context = new ValidationContext(new Validator(), $data, ['key' => 42]);

        $this->assertSame($data, $context->getRawData());
        $this->assertSame(42, $context->getParameter('key'));
    }

    public function testDataSet(): void
    {
        $dataSet = new ArrayDataSet();

        $context = new ValidationContext(new Validator(), null);
        $context->setDataSet($dataSet);

        $this->assertSame($dataSet, $context->getDataSet());
    }

    public function testSetParameter(): void
    {
        $context = new ValidationContext(new Validator(), null);
        $context->setParameter('key', 42);

        $this->assertSame(42, $context->getParameter('key'));
    }

    public function testGetParameter(): void
    {
        $context = new ValidationContext(new Validator(), null, parameters: ['key' => 42]);

        $this->assertSame(42, $context->getParameter('key'));
        $this->assertNull($context->getParameter('non-exists'));
        $this->assertSame(7, $context->getParameter('non-exists', 7));
    }
}
