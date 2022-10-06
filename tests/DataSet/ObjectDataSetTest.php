<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\DataSet;

use PHPUnit\Framework\TestCase;
use ReflectionProperty;
use Yiisoft\Validator\DataSet\ObjectDataSet;
use Yiisoft\Validator\Rule\Equal;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\Tests\Stub\ObjectWithDataSet;
use Yiisoft\Validator\Tests\Stub\ObjectWithDataSetAndRulesProvider;
use Yiisoft\Validator\Tests\Stub\ObjectWithDifferentPropertyVisibility;
use Yiisoft\Validator\Tests\Stub\ObjectWithRulesProvider;

final class ObjectDataSetTest extends TestCase
{
    public function testPropertyVisibility(): void
    {
        $object = new ObjectWithDifferentPropertyVisibility();

        $data = new ObjectDataSet($object);
        $this->assertSame(['name' => '', 'age' => 17, 'number' => 42], $data->getData());
        $this->assertSame('', $data->getAttributeValue('name'));
        $this->assertSame(17, $data->getAttributeValue('age'));
        $this->assertSame(42, $data->getAttributeValue('number'));
        $this->assertNull($data->getAttributeValue('non-exist'));
        $this->assertSame(['name', 'age', 'number'], array_keys($data->getRules()));

        $data = new ObjectDataSet($object, ReflectionProperty::IS_PRIVATE);
        $this->assertSame(['number' => 42], $data->getData());
        $this->assertNull($data->getAttributeValue('name'));
        $this->assertNull($data->getAttributeValue('age'));
        $this->assertSame(42, $data->getAttributeValue('number'));
        $this->assertNull($data->getAttributeValue('non-exist'));
        $this->assertSame(['number'], array_keys($data->getRules()));

        $data = new ObjectDataSet($object, ReflectionProperty::IS_PROTECTED);
        $this->assertSame(['age' => 17], $data->getData());
        $this->assertNull($data->getAttributeValue('name'));
        $this->assertSame(17, $data->getAttributeValue('age'));
        $this->assertNull($data->getAttributeValue('number'));
        $this->assertNull($data->getAttributeValue('non-exist'));
        $this->assertSame(['age'], array_keys($data->getRules()));

        $data = new ObjectDataSet($object, ReflectionProperty::IS_PUBLIC);
        $this->assertSame(['name' => ''], $data->getData());
        $this->assertSame('', $data->getAttributeValue('name'));
        $this->assertNull($data->getAttributeValue('age'));
        $this->assertNull($data->getAttributeValue('number'));
        $this->assertNull($data->getAttributeValue('non-exist'));
        $this->assertSame(['name'], array_keys($data->getRules()));

        $data = new ObjectDataSet($object, ReflectionProperty::IS_PUBLIC|ReflectionProperty::IS_PROTECTED);
        $this->assertSame(['name' => '', 'age' => 17], $data->getData());
        $this->assertSame('', $data->getAttributeValue('name'));
        $this->assertSame(17, $data->getAttributeValue('age'));
        $this->assertNull($data->getAttributeValue('number'));
        $this->assertNull($data->getAttributeValue('non-exist'));
        $this->assertSame(['name', 'age'], array_keys($data->getRules()));
    }

    public function testObjectWithDataSet(): void
    {
        $object = new ObjectWithDataSet();

        $data = new ObjectDataSet($object);

        $this->assertSame(['key1' => 7, 'key2' => 42], $data->getData());
        $this->assertSame(7, $data->getAttributeValue('key1'));
        $this->assertSame(42, $data->getAttributeValue('key2'));

        $this->assertNull($data->getAttributeValue('name'));
        $this->assertNull($data->getAttributeValue('age'));
        $this->assertNull($data->getAttributeValue('number'));
        $this->assertNull($data->getAttributeValue('non-exist'));

        $this->assertSame([], $data->getRules());
    }

    public function testObjectWithRulesProvider(): void
    {
        $object = new ObjectWithRulesProvider();
        $data = new ObjectDataSet($object);
        $rules = $data->getRules();

        $this->assertSame(['name' => '', 'age' => 17, 'number' => 42], $data->getData());

        $this->assertSame('', $data->getAttributeValue('name'));
        $this->assertSame(17, $data->getAttributeValue('age'));
        $this->assertSame(42, $data->getAttributeValue('number'));
        $this->assertNull($data->getAttributeValue('non-exist'));

        $this->assertSame(['age'], array_keys($rules));
        $this->assertCount(2, $rules['age']);
        $this->assertInstanceOf(Required::class, $rules['age'][0]);
        $this->assertInstanceOf(Equal::class, $rules['age'][1]);
    }

    public function testObjectWithDataSetAndRulesProvider(): void
    {
        $object = new ObjectWithDataSetAndRulesProvider();

        $data = new ObjectDataSet($object);
        $rules = $data->getRules();

        $this->assertSame(['key1' => 7, 'key2' => 42], $data->getData());
        $this->assertSame(7, $data->getAttributeValue('key1'));
        $this->assertSame(42, $data->getAttributeValue('key2'));

        $this->assertNull($data->getAttributeValue('name'));
        $this->assertNull($data->getAttributeValue('age'));
        $this->assertNull($data->getAttributeValue('number'));
        $this->assertNull($data->getAttributeValue('non-exist'));

        $this->assertSame(['key1', 'key2'], array_keys($rules));
        $this->assertCount(1, $rules['key1']);
        $this->assertInstanceOf(Required::class, $rules['key1'][0]);
        $this->assertCount(2, $rules['key2']);
        $this->assertInstanceOf(Required::class, $rules['key2'][0]);
        $this->assertInstanceOf(Equal::class, $rules['key2'][1]);
    }
}
