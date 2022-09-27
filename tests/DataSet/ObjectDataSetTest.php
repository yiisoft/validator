<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\DataSet;

use PHPUnit\Framework\TestCase;
use ReflectionProperty;
use Yiisoft\Validator\DataSet\ObjectDataSet;
use Yiisoft\Validator\Rule\Equal;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\Tests\Stub\ObjectWithCallsCount;
use Yiisoft\Validator\Tests\Stub\ObjectWithDataSet;
use Yiisoft\Validator\Tests\Stub\ObjectWithDataSetAndRulesProvider;
use Yiisoft\Validator\Tests\Stub\ObjectWithDifferentPropertyVisibility;
use Yiisoft\Validator\Tests\Stub\ObjectWithRulesProvider;

final class ObjectDataSetTest extends TestCase
{
    public function propertyVisibilityDataProvider(): array
    {
        return [
            [new ObjectWithDifferentPropertyVisibility()],
            [new ObjectWithDifferentPropertyVisibility()], // Not a duplicate. Used to test caching.
        ];
    }

    /**
     * @dataProvider propertyVisibilityDataProvider
     */
    public function testPropertyVisibility(ObjectWithDifferentPropertyVisibility $object): void
    {
        $data = new ObjectDataSet($object);
        $this->assertSame(['name' => '', 'age' => 17, 'number' => 42], $data->getData());
        $this->assertSame('', $data->getAttributeValue('name'));
        $this->assertSame(17, $data->getAttributeValue('age'));
        $this->assertSame(42, $data->getAttributeValue('number'));
        $this->assertSame(null, $data->getAttributeValue('non-exist'));
        $this->assertSame(['name', 'age', 'number'], array_keys($data->getRules()));

        $data = new ObjectDataSet($object, ReflectionProperty::IS_PRIVATE);
        $this->assertSame(['number' => 42], $data->getData());
        $this->assertSame(null, $data->getAttributeValue('name'));
        $this->assertSame(null, $data->getAttributeValue('age'));
        $this->assertSame(42, $data->getAttributeValue('number'));
        $this->assertSame(null, $data->getAttributeValue('non-exist'));
        $this->assertSame(['number'], array_keys($data->getRules()));

        $data = new ObjectDataSet($object, ReflectionProperty::IS_PROTECTED);
        $this->assertSame(['age' => 17], $data->getData());
        $this->assertSame(null, $data->getAttributeValue('name'));
        $this->assertSame(17, $data->getAttributeValue('age'));
        $this->assertSame(null, $data->getAttributeValue('number'));
        $this->assertSame(null, $data->getAttributeValue('non-exist'));
        $this->assertSame(['age'], array_keys($data->getRules()));

        $data = new ObjectDataSet($object, ReflectionProperty::IS_PUBLIC);
        $this->assertSame(['name' => ''], $data->getData());
        $this->assertSame('', $data->getAttributeValue('name'));
        $this->assertSame(null, $data->getAttributeValue('age'));
        $this->assertSame(null, $data->getAttributeValue('number'));
        $this->assertSame(null, $data->getAttributeValue('non-exist'));
        $this->assertSame(['name'], array_keys($data->getRules()));

        $data = new ObjectDataSet($object, ReflectionProperty::IS_PUBLIC|ReflectionProperty::IS_PROTECTED);
        $this->assertSame(['name' => '', 'age' => 17], $data->getData());
        $this->assertSame('', $data->getAttributeValue('name'));
        $this->assertSame(17, $data->getAttributeValue('age'));
        $this->assertSame(null, $data->getAttributeValue('number'));
        $this->assertSame(null, $data->getAttributeValue('non-exist'));
        $this->assertSame(['name', 'age'], array_keys($data->getRules()));
    }

    public function objectWithDataSetDataProvider(): array
    {
        return [
            [new ObjectWithDataSet()],
            [new ObjectWithDataSet()], // Not a duplicate. Used to test caching.
        ];
    }

    /**
     * @dataProvider objectWithDataSetDataProvider
     */
    public function testObjectWithDataSet(ObjectWithDataSet $object): void
    {
        $data = new ObjectDataSet($object);

        $this->assertSame(['key1' => 7, 'key2' => 42], $data->getData());
        $this->assertSame(7, $data->getAttributeValue('key1'));
        $this->assertSame(42, $data->getAttributeValue('key2'));

        $this->assertSame(null, $data->getAttributeValue('name'));
        $this->assertSame(null, $data->getAttributeValue('age'));
        $this->assertSame(null, $data->getAttributeValue('number'));
        $this->assertSame(null, $data->getAttributeValue('non-exist'));

        $this->assertSame([], $data->getRules());
    }

    public function objectWithRulesProvider(): array
    {
        return [
            [new ObjectWithRulesProvider()],
            [new ObjectWithRulesProvider()], // Not a duplicate. Used to test caching.
        ];
    }

    /**
     * @dataProvider objectWithRulesProvider
     */
    public function testObjectWithRulesProvider(ObjectWithRulesProvider $object): void
    {
        $data = new ObjectDataSet($object);
        $rules = $data->getRules();

        $this->assertSame(['name' => '', 'age' => 17, 'number' => 42], $data->getData());

        $this->assertSame('', $data->getAttributeValue('name'));
        $this->assertSame(17, $data->getAttributeValue('age'));
        $this->assertSame(42, $data->getAttributeValue('number'));
        $this->assertSame(null, $data->getAttributeValue('non-exist'));

        $this->assertSame(['age'], array_keys($rules));
        $this->assertCount(2, $rules['age']);
        $this->assertInstanceOf(Required::class, $rules['age'][0]);
        $this->assertInstanceOf(Equal::class, $rules['age'][1]);
    }

    public function objectWithDataSetAndRulesProviderDataProvider(): array
    {
        return [
            [new ObjectWithDataSetAndRulesProvider()],
            [new ObjectWithDataSetAndRulesProvider()], // Not a duplicate. Used to test caching.
        ];
    }

    /**
     * @dataProvider objectWithDataSetAndRulesProviderDataProvider
     */
    public function testObjectWithDataSetAndRulesProvider(ObjectWithDataSetAndRulesProvider $object): void
    {
        $data = new ObjectDataSet($object);
        $rules = $data->getRules();

        $this->assertSame(['key1' => 7, 'key2' => 42], $data->getData());
        $this->assertSame(7, $data->getAttributeValue('key1'));
        $this->assertSame(42, $data->getAttributeValue('key2'));

        $this->assertSame(null, $data->getAttributeValue('name'));
        $this->assertSame(null, $data->getAttributeValue('age'));
        $this->assertSame(null, $data->getAttributeValue('number'));
        $this->assertSame(null, $data->getAttributeValue('non-exist'));

        $this->assertSame(['key1', 'key2'], array_keys($rules));
        $this->assertCount(1, $rules['key1']);
        $this->assertInstanceOf(Required::class, $rules['key1'][0]);
        $this->assertCount(2, $rules['key2']);
        $this->assertInstanceOf(Required::class, $rules['key2'][0]);
        $this->assertInstanceOf(Equal::class, $rules['key2'][1]);
    }

    public function testCaching(): void
    {
        $object1 = new ObjectWithCallsCount();
        $object2 = new ObjectWithCallsCount();

        $data1 = new ObjectDataSet($object1);
        $data2 = new ObjectDataSet($object2);

        $data1->getRules();
        $data2->getRules();

        $data1->getData();
        $data2->getData();

        $this->assertSame(1, ObjectWithCallsCount::$getRulesCallsCount);
        $this->assertSame(1, ObjectWithCallsCount::$getDataCallsCount);
    }
}
