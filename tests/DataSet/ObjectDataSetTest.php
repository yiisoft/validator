<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\DataSet;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;
use stdClass;
use Traversable;
use Yiisoft\Validator\DataSet\ObjectDataSet;
use Yiisoft\Validator\Label;
use Yiisoft\Validator\Rule\Callback;
use Yiisoft\Validator\Rule\Equal;
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\RuleInterface;
use Yiisoft\Validator\Tests\Support\Data\ObjectWithCallbackMethod\ObjectWithCallbackMethod;
use Yiisoft\Validator\Tests\Support\Data\ObjectWithCallbackMethod\ObjectWithNonExistingCallbackMethod;
use Yiisoft\Validator\Tests\Support\Data\ObjectWithDataSet;
use Yiisoft\Validator\Tests\Support\Data\ObjectWithDataSetAndRulesProvider;
use Yiisoft\Validator\Tests\Support\Data\ObjectWithDifferentPropertyVisibility;
use Yiisoft\Validator\Tests\Support\Data\ObjectWithDynamicDataSet;
use Yiisoft\Validator\Tests\Support\Data\ObjectWithIterablePropertyRules;
use Yiisoft\Validator\Tests\Support\Data\ObjectWithLabelsProvider;
use Yiisoft\Validator\Tests\Support\Data\ObjectWithRulesProvider;
use Yiisoft\Validator\Tests\Support\Data\Post;
use Yiisoft\Validator\Tests\Support\Data\TitleTrait;
use Yiisoft\Validator\Tests\Support\Rule\NotRuleAttribute;
use Yiisoft\Validator\Validator;

final class ObjectDataSetTest extends TestCase
{
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
        array $expectedPropertyValuesMap,
        array $expectedRulesKeys
    ): void {
        $dataSets = [
            $initialDataSet,
            $initialDataSet, // Not a duplicate. Used to test caching.
        ];
        foreach ($dataSets as $dataSet) {
            /** @var ObjectDataSet $dataSet */

            $this->assertSame($expectedData, $dataSet->getData());

            foreach ($expectedPropertyValuesMap as $property => $value) {
                $this->assertSame($value, $dataSet->getPropertyValue($property));
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
        $this->assertSame(7, $dataSet->getPropertyValue('key1'));
        $this->assertSame(42, $dataSet->getPropertyValue('key2'));

        $this->assertNull($dataSet->getPropertyValue('name'));
        $this->assertNull($dataSet->getPropertyValue('age'));
        $this->assertNull($dataSet->getPropertyValue('number'));
        $this->assertNull($dataSet->getPropertyValue('non-exist'));

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
            [new ObjectDataSet(new ObjectWithIterablePropertyRules())],
        ];
    }

    /**
     * @dataProvider objectWithRulesProvider
     */
    public function testObjectWithRulesProvider(ObjectDataSet $dataSet): void
    {
        $rules = $dataSet->getRules();

        $this->assertSame(['name' => '', 'age' => 17, 'number' => 42], $dataSet->getData());

        $this->assertSame('', $dataSet->getPropertyValue('name'));
        $this->assertSame(17, $dataSet->getPropertyValue('age'));
        $this->assertSame(42, $dataSet->getPropertyValue('number'));
        $this->assertNull($dataSet->getPropertyValue('non-exist'));

        $this->assertSame(['age', 'name', 'number'], array_keys($rules));
        $this->assertCount(3, $rules['age']);
        $this->assertInstanceOf(Number::class, $rules['age'][0]);
        $this->assertInstanceOf(Required::class, $rules['age'][1]);
        $this->assertInstanceOf(Equal::class, $rules['age'][2]);
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
        $this->assertSame(7, $dataSet->getPropertyValue('key1'));
        $this->assertSame(42, $dataSet->getPropertyValue('key2'));

        $this->assertNull($dataSet->getPropertyValue('name'));
        $this->assertNull($dataSet->getPropertyValue('age'));
        $this->assertNull($dataSet->getPropertyValue('number'));
        $this->assertNull($dataSet->getPropertyValue('non-exist'));

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
        foreach ($dataSet->getRules() as $property => $rules) {
            $actualRules[$property] = $rules instanceof Traversable ? iterator_to_array($rules) : (array) $rules;
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
                    #[Required()]
                    private $property1;
                },
                [
                    'property1' => [
                        new Required(),
                    ],
                ],
            ],
            [
                new class () {
                    use TitleTrait;
                },
                [
                    'title' => [
                        new Length(max: 255),
                    ],
                ],
            ],
            [
                new class () {
                    #[Required()]
                    #[Length(max: 255, skipOnEmpty: true)]
                    private $property1;
                    #[Required()]
                    #[Length(max: 255, skipOnEmpty: true)]
                    private $property2;
                },
                [
                    'property1' => [
                        new Required(),
                        new Length(max: 255, skipOnEmpty: true),
                    ],
                    'property2' => [
                        new Required(),
                        new Length(max: 255, skipOnEmpty: true),
                    ],
                ],
            ],
            [
                new class () {
                    #[Length(max: 255, skipOnEmpty: true)]
                    #[Length(max: 255, skipOnEmpty: false)]
                    private $property1;
                },
                [
                    'property1' => [
                        new Length(max: 255, skipOnEmpty: true),
                        new Length(max: 255, skipOnEmpty: false),
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
        $expectedRules = ['title' => [new Length(max: 255)]];

        $this->assertEquals($expectedRules, $dataSet->getRules());
    }

    /**
     * @link https://github.com/yiisoft/validator/issues/223
     */
    public function testValidateWithCallbackMethod(): void
    {
        $dataSet = new ObjectDataSet(new ObjectWithCallbackMethod());
        $validator = new Validator();

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
        $dataSet = new ObjectDataSet($object);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            sprintf(
                'Method "%s" does not exist in class "%s".',
                'validateName',
                $object::class,
            )
        );
        $dataSet->getRules();
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

    public function testWithoutCache(): void
    {
        $object1 = new stdClass();
        $object1->a = 4;
        $dataSet1 = new ObjectDataSet($object1, useCache: false);

        $object2 = new stdClass();
        $object2->b = 2;
        $dataSet2 = new ObjectDataSet($object2, useCache: false);

        $this->assertSame(['a' => 4], $dataSet1->getData());
        $this->assertSame(['b' => 2], $dataSet2->getData());
    }

    public function testHasPropertyWithDataSetProvided(): void
    {
        $objectDataSet = new ObjectDataSet(new ObjectWithDataSet());
        $this->assertTrue($objectDataSet->hasProperty('key1'));
        $this->assertFalse($objectDataSet->hasProperty('non-existing-key'));
    }

    public function objectWithLabelsProvider(): array
    {
        $dataSet = new ObjectDataSet(new ObjectWithLabelsProvider());
        $expectedResult = ['name' => 'Имя', 'age' => 'Возраст'];

        return [
            [new ObjectDataSet(new ObjectWithLabelsProvider()), $expectedResult],
            [new ObjectDataSet(new ObjectWithLabelsProvider()), $expectedResult], // Not a duplicate. Used to test caching.
            [$dataSet, $expectedResult],
            [$dataSet, $expectedResult], // Not a duplicate. Used to test caching.
            [
                new ObjectDataSet(new class () {
                    #[Required]
                    #[Label('Test label')]
                    public string $property;
                }),
                ['property' => 'Test label'],
            ],
        ];
    }

    /**
     * @dataProvider objectWithLabelsProvider
     */
    public function testObjectWithLabelsProvider(ObjectDataSet $dataSet, array $expected): void
    {
        $this->assertSame($expected, $dataSet->getValidationPropertyLabels());
    }
}
