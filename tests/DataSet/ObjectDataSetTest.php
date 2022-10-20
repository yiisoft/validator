<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\DataSet;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;
use Traversable;
use Yiisoft\Validator\DataSet\ObjectDataSet;
use Yiisoft\Validator\Rule\Callback;
use Yiisoft\Validator\Rule\Equal;
use Yiisoft\Validator\Rule\HasLength;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\RuleInterface;
use Yiisoft\Validator\Tests\Support\Data\ObjectWithCallbackMethod\ObjectWithCallbackMethod;
use Yiisoft\Validator\Tests\Support\Data\ObjectWithCallbackMethod\ObjectWithNonExistingCallbackMethod;
use Yiisoft\Validator\Tests\Support\Data\ObjectWithCallsCount;
use Yiisoft\Validator\Tests\Support\Data\ObjectWithDataSet;
use Yiisoft\Validator\Tests\Support\Data\ObjectWithDataSetAndRulesProvider;
use Yiisoft\Validator\Tests\Support\Data\ObjectWithDifferentPropertyVisibility;
use Yiisoft\Validator\Tests\Support\Data\ObjectWithDynamicDataSet;
use Yiisoft\Validator\Tests\Support\Data\ObjectWithRulesProvider;
use Yiisoft\Validator\Tests\Support\Data\Post;
use Yiisoft\Validator\Tests\Support\Data\TitleTrait;
use Yiisoft\Validator\Tests\Support\Rule\NotRuleAttribute;
use Yiisoft\Validator\Tests\Support\ValidatorFactory;

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
            $initialDataSet, // Not a duplicate. Used to test caching.
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
        ];
    }

    /**
     * @dataProvider objectWithDataSetDataProvider
     */
    public function testObjectWithDataSet(ObjectDataSet $dataSet): void
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
        ];
    }

    /**
     * @dataProvider objectWithRulesProvider
     */
    public function testObjectWithRulesProvider(ObjectDataSet $dataSet): void
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
        ];
    }

    /**
     * @dataProvider objectWithDataSetAndRulesProviderDataProvider
     */
    public function testObjectWithDataSetAndRulesProvider(ObjectDataSet $dataSet): void
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

    /**
     * @dataProvider dataCollectRules
     *
     * @param RuleInterface[]|RuleInterface[][]|RuleInterface[][][] $expectedRules
     */
    public function testCollectRules(object $object, array $expectedRules): void
    {
        $dataSet = new ObjectDataSet($object);

        $actualRules = [];
        foreach ($dataSet->getRules() as $attribute => $rules) {
            $actualRules[$attribute] = $rules instanceof Traversable ? iterator_to_array($rules) : (array) $rules;
        }

        $this->assertEquals($expectedRules, $actualRules);
    }

    public function dataCollectRules(): array
    {
        return [
            [
                new class () {
                },
                [],
            ],
            [
                new class () {
                    private $property1;
                },
                [],
            ],
            [
                new class () {
                    #[NotRuleAttribute]
                    private $property1;
                },
                [],
            ],
            [
                new class () {
                    #[Required(skipOnEmpty: true)]
                    private $property1;
                },
                [
                    'property1' => [
                        new Required(skipOnEmpty: true),
                    ],
                ],
            ],
            [
                new class () {
                    use TitleTrait;
                },
                [
                    'title' => [
                        new HasLength(max: 255),
                    ],
                ],
            ],
            [
                new class () {
                    #[Required(skipOnEmpty: true)]
                    #[HasLength(max: 255, skipOnEmpty: true)]
                    private $property1;
                    #[Required(skipOnEmpty: true)]
                    #[HasLength(max: 255, skipOnEmpty: true)]
                    private $property2;
                },
                [
                    'property1' => [
                        new Required(skipOnEmpty: true),
                        new HasLength(max: 255, skipOnEmpty: true),
                    ],
                    'property2' => [
                        new Required(skipOnEmpty: true),
                        new HasLength(max: 255, skipOnEmpty: true),
                    ],
                ],
            ],
            [
                new class () {
                    #[HasLength(max: 255, skipOnEmpty: true)]
                    #[HasLength(max: 255, skipOnEmpty: false)]
                    private $property1;
                },
                [
                    'property1' => [
                        new HasLength(max: 255, skipOnEmpty: true),
                        new HasLength(max: 255, skipOnEmpty: false),
                    ],
                ],
            ],
        ];
    }

    /**
     * @link https://github.com/yiisoft/validator/issues/198
     */
    public function testGetRulesViaTraits(): void
    {
        $dataSet = new ObjectDataSet(new Post());
        $expectedRules = ['title' => [new HasLength(max: 255)]];

        $this->assertEquals($expectedRules, $dataSet->getRules());
    }

    /**
     * @link https://github.com/yiisoft/validator/issues/223
     */
    public function testValidateWithCallbackMethod(): void
    {
        $dataSet = new ObjectDataSet(new ObjectWithCallbackMethod());
        $validator = ValidatorFactory::make();

        /** @var array $rules */
        $rules = $dataSet->getRules();
        $this->assertSame(['name'], array_keys($rules));
        $this->assertCount(1, $rules['name']);
        $this->assertInstanceOf(Callback::class, $rules['name'][0]);

        $result = $validator->validate(['name' => 'bar'], $rules);
        $this->assertSame(['name' => ['Value must be "foo"!']], $result->getErrorMessagesIndexedByPath());
    }

    public function testValidateWithWrongCallbackMethod(): void
    {
        $object = new ObjectWithNonExistingCallbackMethod();
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            sprintf(
                'Method "%s" does not exist in class "%s".',
                'validateName',
                $object::class,
            )
        );
        new ObjectDataSet($object);
    }

    public function objectWithDynamicDataSetProvider(): array
    {
        return [
            [
                new ObjectDataSet(new ObjectWithDynamicDataSet('A')),
                ['name' => 'A'],
            ],
            [
                new ObjectDataSet(new ObjectWithDynamicDataSet('B')),
                ['name' => 'B'],
            ],
        ];
    }

    /**
     * @dataProvider objectWithDynamicDataSetProvider
     */
    public function testObjectWithDynamicDataSet(ObjectDataSet $dataSet, array $expectedData): void
    {
        $this->assertSame($expectedData, $dataSet->getData());
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
                2,
                2,
            ],
        ];
    }

    /**
     * @param ObjectDataSet[] $objectDataSets
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
