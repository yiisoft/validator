<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Yiisoft\Validator\PropertyTranslator\ArrayPropertyTranslator;
use Yiisoft\Validator\PropertyTranslator\NullPropertyTranslator;
use Yiisoft\Validator\DataSet\ArrayDataSet;
use Yiisoft\Validator\ValidationContext;
use Yiisoft\Validator\Validator;

final class ValidationContextTest extends TestCase
{
    public function testGetDataSetWithoutDataSet(): void
    {
        $context = new ValidationContext();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Data set in validation context is not set.');
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
        $this->expectExceptionMessage('Validator is not set in validation context.');
        $context->validate(42);
    }

    public function testGetRawDataWithoutRawData(): void
    {
        $context = new ValidationContext();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Validator is not set in validation context.');
        $context->getRawData();
    }

    public function testSetContextDataOnce(): void
    {
        $validator = new Validator();
        $data1 = ['1'];
        $data2 = ['2'];
        $dataSet1 = new ArrayDataSet($data1);
        $dataSet2 = new ArrayDataSet($data2);

        $context = (new ValidationContext())
            ->setContextDataOnce($validator, new NullPropertyTranslator(), $data1, $dataSet1)
            ->setContextDataOnce($validator, new NullPropertyTranslator(), $data2, $dataSet2);

        $this->assertSame($data1, $context->getRawData());
        $this->assertSame($dataSet1, $context->getGlobalDataSet());
    }

    public static function dataTranslatedPropertyWithoutTranslator(): array
    {
        return [
            'null' => ['Value'],
            'string' => ['test'],
        ];
    }

    #[DataProvider('dataTranslatedPropertyWithoutTranslator')]
    public function testTranslatedPropertyWithoutTranslator(?string $property): void
    {
        $context = new ValidationContext();
        $context->setProperty($property);

        $this->assertSame($property, $context->getTranslatedProperty());
    }

    public function testSetPropertyTranslator(): void
    {
        $translator = new ArrayPropertyTranslator(['name' => 'Имя']);

        $context = (new ValidationContext())
            ->setPropertyTranslator($translator)
            ->setProperty('name');

        $this->assertSame('Имя', $context->getTranslatedProperty());
    }

    public function testSetPropertyLabel(): void
    {
        $context = (new ValidationContext())
            ->setProperty('name')
            ->setPropertyLabel('first Name');

        $this->assertSame('first Name', $context->getPropertyLabel());
        $this->assertSame('first Name', $context->getTranslatedProperty());
        $this->assertSame('First Name', $context->getCapitalizedTranslatedProperty());
    }

    public function testSetPropertyLabelWithTranslator(): void
    {
        $translator = new ArrayPropertyTranslator(['First Name' => 'Имя']);

        $context = (new ValidationContext())
            ->setPropertyTranslator($translator)
            ->setProperty('name')
            ->setPropertyLabel('First Name');

        $this->assertSame('Имя', $context->getTranslatedProperty());
    }
}
