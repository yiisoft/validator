<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests;

use PHPUnit\Framework\TestCase;
use RuntimeException;
use Yiisoft\Validator\AttributeTranslator\ArrayAttributeTranslator;
use Yiisoft\Validator\AttributeTranslator\NullAttributeTranslator;
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
            ->setContextDataOnce($validator, new NullAttributeTranslator(), $data1, $dataSet1)
            ->setContextDataOnce($validator, new NullAttributeTranslator(), $data2, $dataSet2);

        $this->assertSame($data1, $context->getRawData());
        $this->assertSame($dataSet1, $context->getGlobalDataSet());
    }

    public function dataTranslatedAttributeWithoutTranslator(): array
    {
        return [
            'null' => ['The value'],
            'string' => ['test'],
        ];
    }

    /**
     * @dataProvider dataTranslatedAttributeWithoutTranslator
     */
    public function testTranslatedAttributeWithoutTranslator(?string $attribute): void
    {
        $context = new ValidationContext();
        $context->setAttribute($attribute);

        $this->assertSame($attribute, $context->getTranslatedAttribute());
    }

    public function testSetAttributeTranslator(): void
    {
        $translator = new ArrayAttributeTranslator(['name' => 'Имя']);

        $context = (new ValidationContext())
            ->setAttributeTranslator($translator)
            ->setAttribute('name');

        $this->assertSame('Имя', $context->getTranslatedAttribute());
    }

    public function testSetAttributeLabel(): void
    {
        $context = (new ValidationContext())
            ->setAttribute('name')
            ->setAttributeLabel('First Name');

        $this->assertSame('First Name', $context->getAttributeLabel());
        $this->assertSame('First Name', $context->getTranslatedAttribute());
    }

    public function testSetAttributeLabelWithTranslator(): void
    {
        $translator = new ArrayAttributeTranslator(['First Name' => 'Имя']);

        $context = (new ValidationContext())
            ->setAttributeTranslator($translator)
            ->setAttribute('name')
            ->setAttributeLabel('First Name');

        $this->assertSame('Имя', $context->getTranslatedAttribute());
    }
}
