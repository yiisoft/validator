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
    public function testGetDataSetWithoutDataSet(): void
    {
        $context = new ValidationContext();

        $this->expectException(RuntimeException::class);
        $this->expectErrorMessage('Data set in validation context is not set.');
        $context->getDataSet();
    }

    public function testConstructor(): void
    {
        $context = new ValidationContext(['key' => 42]);

        $this->assertSame(42, $context->getParameter('key'));
    }

    public function testDataSet(): void
    {
        $dataSet = new ArrayDataSet();

        $context = new ValidationContext();
        $context->setDataSet($dataSet);

        $this->assertSame($dataSet, $context->getDataSet());
    }

    public function testSetParameter(): void
    {
        $context = new ValidationContext();
        $context->setParameter('key', 42);

        $this->assertSame(42, $context->getParameter('key'));
    }

    public function testGetParameter(): void
    {
        $context = new ValidationContext(['key' => 42]);

        $this->assertSame(42, $context->getParameter('key'));
        $this->assertNull($context->getParameter('non-exists'));
        $this->assertSame(7, $context->getParameter('non-exists', 7));
    }

    public function testValidateWithoutValidator(): void
    {
        $context = new ValidationContext();

        $this->expectException(RuntimeException::class);
        $this->expectErrorMessage('Validator and raw data in validation context is not set.');
        $context->validate(42);
    }

    public function tesGetRawDataWithoutRawData(): void
    {
        $context = new ValidationContext();

        $this->expectException(RuntimeException::class);
        $this->expectErrorMessage('Validator and raw data in validation context is not set.');
        $context->getRawData();
    }

    public function testSetValidatorAndRawDataOnce(): void
    {
        $validator = new Validator();

        $context = new ValidationContext();

        $context
            ->setValidatorAndRawDataOnce($validator, 1)
            ->setValidatorAndRawDataOnce($validator, 2);

        $this->assertSame(1, $context->getRawData());
    }
}
