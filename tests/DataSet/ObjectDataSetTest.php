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
use Yiisoft\Validator\Tests\Support\Data\ObjectWithCallbackMethod\ObjectWithNonPublicCallbackMethod;
use Yiisoft\Validator\Tests\Support\Data\ObjectWithCallbackMethod\ObjectWithNonStaticCallbackMethod;
use Yiisoft\Validator\Tests\Support\Data\ObjectWithDataSet;
use Yiisoft\Validator\Tests\Support\Data\ObjectWithDataSetAndRulesProvider;
use Yiisoft\Validator\Tests\Support\Data\ObjectWithDifferentPropertyVisibility;
use Yiisoft\Validator\Tests\Support\Data\ObjectWithRulesProvider;
use Yiisoft\Validator\Tests\Support\Data\Post;
use Yiisoft\Validator\Tests\Support\Data\TitleTrait;
use Yiisoft\Validator\Tests\Support\Rule\NotRuleAttribute;
use Yiisoft\Validator\Tests\Support\ValidatorFactory;

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

    public function validateWithWrongCallbackMethodDataProvider(): array
    {
        return [
            [new ObjectWithNonExistingCallbackMethod()],
            [new ObjectWithNonPublicCallbackMethod()],
            [new ObjectWithNonStaticCallbackMethod()],
        ];
    }

    /**
     * @link https://github.com/yiisoft/validator/issues/223
     * @dataProvider validateWithWrongCallbackMethodDataProvider
     */
    public function testValidateWithWrongCallbackMethod(object $object): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Method must exist and have public and static modifers.');
        new ObjectDataSet($object);
    }
}
