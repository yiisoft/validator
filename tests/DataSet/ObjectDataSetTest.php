<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\DataSet;

use PHPUnit\Framework\TestCase;
use ReflectionProperty;
use Yiisoft\Validator\DataSet\CacheObjectDataSetDecorator;
use Yiisoft\Validator\DataSet\ObjectDataSet;
use Yiisoft\Validator\ObjectDataSetInterface;
use Yiisoft\Validator\Rule\Equal;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\Tests\Stub\ObjectWithCallsCount;
use Yiisoft\Validator\Tests\Stub\ObjectWithDataSet;
use Yiisoft\Validator\Tests\Stub\ObjectWithDataSetAndRulesProvider;
use Yiisoft\Validator\Tests\Stub\ObjectWithDifferentPropertyVisibility;
use Yiisoft\Validator\Tests\Stub\ObjectWithRulesProvider;

final class ObjectDataSetTest extends TestCase
{
    public function setUp(): void
    {
        ObjectWithCallsCount::$getRulesCallsCount = 0;
        ObjectWithCallsCount::$getDataCallsCount = 0;
    }

    public function propertyVisibilityDataProvider(): array
    {
        return [
            [
                new ObjectDataSet(new ObjectWithDifferentPropertyVisibility()),
                ['name' => '', 'age' => 17, 'number' => 42],
                ['name' => '', 'age' => 17, 'number' => 42, 'non-exist' => null],
                ['name', 'age', 'number'],
            ],
            [
                new ObjectDataSet(new ObjectWithDifferentPropertyVisibility(), ReflectionProperty::IS_PRIVATE),
                ['number' => 42],
                ['name' => null, 'age' => null, 'number' => 42, 'non-exist' => null],
                ['number'],
            ],
            [
                new ObjectDataSet(new ObjectWithDifferentPropertyVisibility(), ReflectionProperty::IS_PROTECTED),
                ['age' => 17],
                ['name' => null, 'age' => 17, 'number' => null, 'non-exist' => null],
                ['age'],
            ],
            [
                new ObjectDataSet(new ObjectWithDifferentPropertyVisibility(), ReflectionProperty::IS_PUBLIC),
                ['name' => ''],
                ['name' => '', 'age' => null, 'number' => null, 'non-exist' => null],
                ['name'],
            ],
            [
                new ObjectDataSet(
                    new ObjectWithDifferentPropertyVisibility(),
                    ReflectionProperty::IS_PUBLIC | ReflectionProperty::IS_PROTECTED
                ),
                ['name' => '', 'age' => 17],
                ['name' => '', 'age' => 17, 'number' => null, 'non-exist' => null],
                ['name', 'age'],
            ],
        ];
    }

    /**
     * @dataProvider propertyVisibilityDataProvider
     */
    public function testPropertyVisibility(
        ObjectDataSet $initialDataSet,
        array $expectedData,
        array $expectedAttributeValuesMap,
        array $expectedRulesKeys
    ): void {
        $dataSets = [
            $initialDataSet,
            new CacheObjectDataSetDecorator($initialDataSet),
            new CacheObjectDataSetDecorator($initialDataSet), // Not a duplicate. Used to test caching.
        ];
        foreach ($dataSets as $dataSet) {
            /** @var ObjectDataSet $dataSet */

            $this->assertSame($expectedData, $dataSet->getData());

            foreach ($expectedAttributeValuesMap as $attribute => $value) {
                $this->assertSame($value, $dataSet->getAttributeValue($attribute));
            }

            $this->assertSame($expectedRulesKeys, array_keys($dataSet->getRules()));
        }
    }

    public function objectWithDataSetDataProvider(): array
    {
        $dataSet = new ObjectDataSet(new ObjectWithDataSet());

        return [
            [new ObjectDataSet(new ObjectWithDataSet())],
            [new ObjectDataSet(new ObjectWithDataSet())], // Not a duplicate. Used to test caching.
            [$dataSet],
            [$dataSet], // Not a duplicate. Used to test caching.
            [new CacheObjectDataSetDecorator(new ObjectDataSet(new ObjectWithDataSet()))],
            // Not a duplicate. Used to test caching.
            [new CacheObjectDataSetDecorator(new ObjectDataSet(new ObjectWithDataSet()))],
        ];
    }

    /**
     * @dataProvider objectWithDataSetDataProvider
     */
    public function testObjectWithDataSet(ObjectDataSetInterface $dataSet): void
    {
        $this->assertSame(['key1' => 7, 'key2' => 42], $dataSet->getData());
        $this->assertSame(7, $dataSet->getAttributeValue('key1'));
        $this->assertSame(42, $dataSet->getAttributeValue('key2'));

        $this->assertNull($dataSet->getAttributeValue('name'));
        $this->assertNull($dataSet->getAttributeValue('age'));
        $this->assertNull($dataSet->getAttributeValue('number'));
        $this->assertNull($dataSet->getAttributeValue('non-exist'));

        $this->assertSame([], $dataSet->getRules());
    }

    public function objectWithRulesProvider(): array
    {
        $dataSet = new ObjectDataSet(new ObjectWithRulesProvider());

        return [
            [new ObjectDataSet(new ObjectWithRulesProvider())],
            [new ObjectDataSet(new ObjectWithRulesProvider())], // Not a duplicate. Used to test caching.
            [$dataSet],
            [$dataSet], // Not a duplicate. Used to test caching.
            [new CacheObjectDataSetDecorator(new ObjectDataSet(new ObjectWithRulesProvider()))],
            // Not a duplicate. Used to test caching.
            [new CacheObjectDataSetDecorator(new ObjectDataSet(new ObjectWithRulesProvider()))],
        ];
    }

    /**
     * @dataProvider objectWithRulesProvider
     */
    public function testObjectWithRulesProvider(ObjectDataSetInterface $dataSet): void
    {
        $rules = $dataSet->getRules();

        $this->assertSame(['name' => '', 'age' => 17, 'number' => 42], $dataSet->getData());

        $this->assertSame('', $dataSet->getAttributeValue('name'));
        $this->assertSame(17, $dataSet->getAttributeValue('age'));
        $this->assertSame(42, $dataSet->getAttributeValue('number'));
        $this->assertNull($dataSet->getAttributeValue('non-exist'));

        $this->assertSame(['age'], array_keys($rules));
        $this->assertCount(2, $rules['age']);
        $this->assertInstanceOf(Required::class, $rules['age'][0]);
        $this->assertInstanceOf(Equal::class, $rules['age'][1]);
    }

    public function objectWithDataSetAndRulesProviderDataProvider(): array
    {
        $dataSet = new ObjectDataSet(new ObjectWithDataSetAndRulesProvider());

        return [
            [new ObjectDataSet(new ObjectWithDataSetAndRulesProvider())],
            [new ObjectDataSet(new ObjectWithDataSetAndRulesProvider())], // Not a duplicate. Used to test caching.
            [$dataSet],
            [$dataSet], // Not a duplicate. Used to test caching.
            [new CacheObjectDataSetDecorator(new ObjectDataSet(new ObjectWithDataSetAndRulesProvider()))],
            // Not a duplicate. Used to test caching.
            [new CacheObjectDataSetDecorator(new ObjectDataSet(new ObjectWithDataSetAndRulesProvider()))],
        ];
    }

    /**
     * @dataProvider objectWithDataSetAndRulesProviderDataProvider
     */
    public function testObjectWithDataSetAndRulesProvider(ObjectDataSetInterface $dataSet): void
    {
        $rules = $dataSet->getRules();

        $this->assertSame(['key1' => 7, 'key2' => 42], $dataSet->getData());
        $this->assertSame(7, $dataSet->getAttributeValue('key1'));
        $this->assertSame(42, $dataSet->getAttributeValue('key2'));

        $this->assertNull($dataSet->getAttributeValue('name'));
        $this->assertNull($dataSet->getAttributeValue('age'));
        $this->assertNull($dataSet->getAttributeValue('number'));
        $this->assertNull($dataSet->getAttributeValue('non-exist'));

        $this->assertSame(['key1', 'key2'], array_keys($rules));
        $this->assertCount(1, $rules['key1']);
        $this->assertInstanceOf(Required::class, $rules['key1'][0]);
        $this->assertCount(2, $rules['key2']);
        $this->assertInstanceOf(Required::class, $rules['key2'][0]);
        $this->assertInstanceOf(Equal::class, $rules['key2'][1]);
    }

    public function cachingDataProvider(): array
    {
        $objectDataSet = new ObjectDataSet(new ObjectWithCallsCount());

        return [
            [
                [
                    new ObjectDataSet(new ObjectWithCallsCount()),
                    new ObjectDataSet(new ObjectWithCallsCount()), // Not a duplicate. Used to test caching.
                ],
                2,
                2,
            ],
            [
                [
                    $objectDataSet,
                    $objectDataSet, // Not a duplicate. Used to test caching.
                ],
                1,
                2,
            ],
            [
                [
                    new CacheObjectDataSetDecorator(new ObjectDataSet(new ObjectWithCallsCount())),
                    // Not a duplicate. Used to test caching.
                    new CacheObjectDataSetDecorator(new ObjectDataSet(new ObjectWithCallsCount())),
                ],
                1,
                1,
            ],
        ];
    }

    /**
     * @param ObjectDataSetInterface[] $objectDataSets
     * @dataProvider cachingDataProvider
     */
    public function testCaching(array $objectDataSets, int $expectedRulesCallsCount, int $expectedDataCallsCount): void
    {
        foreach ($objectDataSets as $objectDataSet) {
            $objectDataSet->getRules();
        }

        foreach ($objectDataSets as $objectDataSet) {
            $objectDataSet->getData();
        }

        $this->assertSame($expectedRulesCallsCount, ObjectWithCallsCount::$getRulesCallsCount);
        $this->assertSame($expectedDataCallsCount, ObjectWithCallsCount::$getDataCallsCount);
    }
}
