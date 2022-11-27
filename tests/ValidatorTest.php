<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;
use stdClass;
use Yiisoft\Validator\DataSet\ArrayDataSet;
use Yiisoft\Validator\DataSet\ObjectDataSet;
use Yiisoft\Validator\DataSetHelper;
use Yiisoft\Validator\DataSetInterface;
use Yiisoft\Validator\Error;
use Yiisoft\Validator\Exception\RuleHandlerInterfaceNotImplementedException;
use Yiisoft\Validator\Exception\RuleHandlerNotFoundException;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\Boolean;
use Yiisoft\Validator\Rule\CompareTo;
use Yiisoft\Validator\Rule\HasLength;
use Yiisoft\Validator\Rule\InRange;
use Yiisoft\Validator\Rule\IsTrue;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\RuleInterface;
use Yiisoft\Validator\RulesProviderInterface;
use Yiisoft\Validator\SimpleRuleHandlerContainer;
use Yiisoft\Validator\EmptyCriteria\WhenEmpty;
use Yiisoft\Validator\EmptyCriteria\WhenNull;
use Yiisoft\Validator\Tests\RulesProvider\AttributesRulesProviderTest;
use Yiisoft\Validator\Tests\Support\Data\EachNestedObjects\Foo;
use Yiisoft\Validator\Tests\Support\Data\IteratorWithBooleanKey;
use Yiisoft\Validator\Tests\Support\Data\ObjectWithDataSetAndRulesProvider;
use Yiisoft\Validator\Tests\Support\Data\ObjectWithDifferentPropertyVisibility;
use Yiisoft\Validator\Tests\Support\Data\ObjectWithPostValidationHook;
use Yiisoft\Validator\Tests\Support\Data\ObjectWithRulesProvider;
use Yiisoft\Validator\Tests\Support\ValidatorFactory;
use Yiisoft\Validator\Tests\Support\Rule\NotNullRule\NotNull;
use Yiisoft\Validator\Tests\Support\Data\ObjectWithDataSet;
use Yiisoft\Validator\Tests\Support\Rule\StubRule\StubRule;
use Yiisoft\Validator\Tests\Support\Data\ObjectWithAttributesOnly;
use Yiisoft\Validator\Tests\Support\TranslatorFactory;
use Yiisoft\Validator\ValidationContext;
use Yiisoft\Validator\Validator;
use Yiisoft\Validator\ValidatorInterface;

use function extension_loaded;

class ValidatorTest extends TestCase
{
    public function setUp(): void
    {
        ObjectWithPostValidationHook::$hookCalled = false;
    }

    public function testBase(): void
    {
        $validator = new Validator();

        $result = $validator->validate(new ObjectWithAttributesOnly());

        $this->assertFalse($result->isValid());
        $this->assertSame(
            ['name' => ['This value must contain at least 5 characters.']],
            $result->getErrorMessagesIndexedByPath()
        );
    }

    public function dataDataAndRulesCombinations(): array
    {
        return [
            'pure-object-and-array-of-rules' => [
                [
                    'number' => ['Value must be no less than 77.'],
                ],
                new ObjectWithDifferentPropertyVisibility(),
                [
                    'age' => new Number(max: 100),
                    'number' => new Number(min: 77),
                ],
            ],
            'pure-object-and-no-rules' => [
                [
                    'name' => ['Value cannot be blank.'],
                    'age' => ['Value must be no less than 21.'],
                ],
                new ObjectWithDifferentPropertyVisibility(),
                null,
            ],
            'dataset-object-and-array-of-rules' => [
                [
                    'key1' => ['Value must be no less than 21.'],
                ],
                new ObjectWithDataSet(),
                [
                    'key1' => new Number(min: 21),
                ],
            ],
            'dataset-object-and-no-rules' => [
                [],
                new ObjectWithDataSet(),
                null,
            ],
            'rules-provider-object-and-array-of-rules' => [
                [
                    'number' => ['Value must be no greater than 7.'],
                ],
                new ObjectWithRulesProvider(),
                [
                    'age' => new Number(max: 100),
                    'number' => new Number(max: 7),
                ],
            ],
            'rules-provider-object-and-no-rules' => [
                [
                    'age' => ['Value must be equal to "25".'],
                ],
                new ObjectWithRulesProvider(),
                null,
            ],
            'rules-provider-and-dataset-object-and-array-of-rules' => [
                [
                    'key2' => ['Value must be no greater than 7.'],
                ],
                new ObjectWithDataSetAndRulesProvider(),
                [
                    'key2' => new Number(max: 7),
                ],
            ],
            'rules-provider-and-dataset-object-and-no-rules' => [
                [
                    'key2' => ['Value must be equal to "99".'],
                ],
                new ObjectWithDataSetAndRulesProvider(),
                null,
            ],
            'array-and-array-of-rules' => [
                [
                    'key2' => ['Value must be no greater than 7.'],
                ],
                ['key1' => 15, 'key2' => 99],
                [
                    'key1' => new Number(max: 100),
                    'key2' => new Number(max: 7),
                ],
            ],
            'array-and-no-rules' => [
                [],
                ['key1' => 15, 'key2' => 99],
                null,
            ],
            'scalar-and-array-of-rules' => [
                [
                    '' => ['Value must be no greater than 7.'],
                ],
                42,
                [
                    new Number(max: 7),
                ],
            ],
            'scalar-and-no-rules' => [
                [],
                42,
                null,
            ],
            'array-and-rules-provider' => [
                [
                    'age' => ['Value must be no less than 18.'],
                ],
                [
                    'age' => 17,
                ],
                new class () implements RulesProviderInterface {
                    public function getRules(): iterable
                    {
                        return [
                            'age' => [new Number(min: 18)],
                        ];
                    }
                },
            ],
            'array-and-object' => [
                [
                    'name' => ['Value not passed.'],
                    'bars' => ['Value must be array or iterable.'],
                ],
                [],
                new Foo(),
            ],
        ];
    }

    /**
     * @dataProvider dataDataAndRulesCombinations
     */
    public function testDataAndRulesCombinations(
        array $expectedErrorMessages,
        mixed $data,
        iterable|object|string|null $rules,
    ): void {
        $validator = ValidatorFactory::make();
        $result = $validator->validate($data, $rules);
        $this->assertSame($expectedErrorMessages, $result->getErrorMessagesIndexedByAttribute());
    }

    public function dataRulesPropertyVisibility(): array
    {
        return [
            'default' => [
                null,
                ['age' => 20, 'number' => 101],
                new ObjectWithDifferentPropertyVisibility(),
                [
                    'name' => ['Value not passed.'],
                    'age' => ['Value must be no less than 21.'],
                    'number' => ['Value must be no greater than 100.'],
                ],
            ],
            'custom' => [
                ReflectionProperty::IS_PRIVATE,
                ['age' => 20, 'number' => 101],
                new ObjectWithDifferentPropertyVisibility(),
                [
                    'number' => ['Value must be no greater than 100.'],
                ],
            ],
        ];
    }

    /**
     * More variations are covered in {@see AttributesRulesProviderTest::testPropertyVisibility()}.
     *
     * @dataProvider dataRulesPropertyVisibility
     */
    public function testRulesPropertyVisibility(
        int|null $rulesPropertyVisibility,
        array $data,
        object $source,
        array $expectedErrorMessages,
    ): void {
        $arguments = [
            new SimpleRuleHandlerContainer(),
            (new TranslatorFactory())->create(),
        ];
        if ($rulesPropertyVisibility !== null) {
            $arguments[] = $rulesPropertyVisibility;
        }

        $validator = new Validator(...$arguments);

        $result = $validator->validate($data, $source);
        $this->assertSame($expectedErrorMessages, $result->getErrorMessagesIndexedByPath());
    }

    public function dataWithEmptyArrayOfRules(): array
    {
        return [
            'pure-object-and-no-rules' => [new ObjectWithDifferentPropertyVisibility()],
            'dataset-object-and-no-rules' => [new ObjectWithDataSet()],
            'rules-provider-object' => [new ObjectWithRulesProvider()],
            'rules-provider-and-dataset-object' => [new ObjectWithDataSetAndRulesProvider()],
            'array' => [['key1' => 15, 'key2' => 99]],
            'scalar' => [42],
        ];
    }

    /**
     * @dataProvider dataWithEmptyArrayOfRules
     */
    public function testWithEmptyArrayOfRules(mixed $data): void
    {
        $validator = ValidatorFactory::make();
        $result = $validator->validate($data, []);

        $this->assertTrue($result->isValid());
    }

    public function testAddingRulesViaConstructor(): void
    {
        $dataObject = new ArrayDataSet(['bool' => true, 'int' => 41]);
        $validator = ValidatorFactory::make();
        $result = $validator->validate($dataObject, [
            'bool' => [new Boolean()],
            'int' => [
                new Number(asInteger: true),
                new Number(asInteger: true, min: 44),
                static function (mixed $value): Result {
                    $result = new Result();
                    if ($value !== 42) {
                        $result->addError('Value should be 42!', ['int']);
                    }

                    return $result;
                },
            ],
        ]);

        $this->assertTrue($result->isAttributeValid('bool'));
        $this->assertFalse($result->isAttributeValid('int'));
    }

    public function diverseTypesDataProvider(): array
    {
        $class = new stdClass();
        $class->property = true;

        return [
            'object' => [new ObjectDataSet($class, useCache: false)],
            'true' => [true],
            'non-empty-string' => ['true'],
            'integer' => [12345],
            'float' => [12.345],
            'false' => [false],
        ];
    }

    /**
     * @dataProvider diverseTypesDataProvider
     */
    public function testDiverseTypes($dataSet): void
    {
        $validator = ValidatorFactory::make();
        $result = $validator->validate($dataSet, [new Required()]);

        $this->assertTrue($result->isValid());
    }

    public function testNullAsDataSet(): void
    {
        $validator = ValidatorFactory::make();
        $result = $validator->validate(null, ['property' => [new CompareTo(null)]]);

        $this->assertTrue($result->isValid());
    }

    public function testPreValidation(): void
    {
        $validator = ValidatorFactory::make();
        $result = $validator->validate(
            new ArrayDataSet(['property' => '']),
            ['property' => [new Required(when: static fn (mixed $value, ?ValidationContext $context): bool => false)]],
        );

        $this->assertTrue($result->isValid());
    }

    public function testRuleHandlerWithoutImplement(): void
    {
        $ruleHandler = new class () {
        };
        $validator = ValidatorFactory::make();

        $this->expectException(RuleHandlerInterfaceNotImplementedException::class);
        $validator->validate(new ArrayDataSet(['property' => '']), [
            'property' => [
                new class ($ruleHandler) implements RuleInterface {
                    public function __construct(private $ruleHandler)
                    {
                    }

                    public function getName(): string
                    {
                        return 'test';
                    }

                    public function getHandlerClassName(): string
                    {
                        return $this->ruleHandler::class;
                    }
                },
            ],
        ]);
    }

    public function testRuleWithoutHandler(): void
    {
        $this->expectException(RuleHandlerNotFoundException::class);

        $validator = ValidatorFactory::make();
        $validator->validate(new ArrayDataSet(['property' => '']), [
            'property' => [
                new class () implements RuleInterface {
                    public function getName(): string
                    {
                        return 'test';
                    }

                    public function getHandlerClassName(): string
                    {
                        return 'NonExistClass';
                    }
                },
            ],
        ]);
    }

    public function requiredDataProvider(): array
    {
        $strictRules = [
            'orderBy' => [new Required()],
            'sort' => [
                new InRange(
                    ['asc', 'desc'],
                    skipOnEmpty: static fn (mixed $value, bool $isAttributeMissing): bool => $isAttributeMissing
                ),
            ],
        ];
        $notStrictRules = [
            'orderBy' => [new Required()],
            'sort' => [
                new InRange(
                    ['asc', 'desc'],
                    skipOnEmpty: static fn (
                        mixed $value,
                        bool $isAttributeMissing
                    ): bool => $isAttributeMissing || $value === ''
                ),
            ],
        ];

        return [
            [
                ['merchantId' => [new Required(), new Number(asInteger: true)]],
                new ArrayDataSet(['merchantId' => null]),
                [
                    new Error(
                        'Value cannot be blank.',
                        [],
                        ['merchantId']
                    ),
                    new Error(
                        'The allowed types are integer, float and string.',
                        ['attribute' => 'merchantId', 'type' => 'null'],
                        ['merchantId']
                    ),
                ],
            ],
            [
                ['merchantId' => [new Required(), new Number(asInteger: true, skipOnError: true)]],
                new ArrayDataSet(['merchantId' => null]),
                [new Error('Value cannot be blank.', [], ['merchantId'])],
            ],
            [
                ['merchantId' => [new Required(), new Number(asInteger: true, skipOnError: true)]],
                new ArrayDataSet(['merchantIdd' => 1]),
                [new Error('Value not passed.', [], ['merchantId'])],
            ],

            [
                $strictRules,
                new ArrayDataSet(['orderBy' => 'name', 'sort' => 'asc']),
                [],
            ],
            [
                $notStrictRules,
                new ArrayDataSet(['orderBy' => 'name', 'sort' => 'asc']),
                [],
            ],

            [
                $strictRules,
                new ArrayDataSet(['orderBy' => 'name', 'sort' => 'desc']),
                [],
            ],
            [
                $notStrictRules,
                new ArrayDataSet(['orderBy' => 'name', 'sort' => 'desc']),
                [],
            ],

            [
                $strictRules,
                new ArrayDataSet(['orderBy' => 'name', 'sort' => 'up']),
                [new Error('This value is invalid.', ['attribute' => 'sort'], ['sort'])],
            ],
            [
                $notStrictRules,
                new ArrayDataSet(['orderBy' => 'name', 'sort' => 'up']),
                [new Error('This value is invalid.', ['attribute' => 'sort'], ['sort'])],
            ],

            [
                $strictRules,
                new ArrayDataSet(['orderBy' => 'name', 'sort' => '']),
                [new Error('This value is invalid.', ['attribute' => 'sort'], ['sort'])],
            ],
            [
                $notStrictRules,
                new ArrayDataSet(['orderBy' => 'name', 'sort' => '']),
                [],
            ],

            [
                $strictRules,
                new ArrayDataSet(['orderBy' => 'name']),
                [],
            ],
            [
                $notStrictRules,
                new ArrayDataSet(['orderBy' => 'name']),
                [],
            ],

            [
                $strictRules,
                new ArrayDataSet(['orderBy' => '']),
                [new Error('Value cannot be blank.', [], ['orderBy'])],
            ],
            [
                $notStrictRules,
                new ArrayDataSet(['orderBy' => '']),
                [new Error('Value cannot be blank.', [], ['orderBy'])],
            ],

            [
                $strictRules,
                new ArrayDataSet([]),
                [new Error('Value not passed.', [], ['orderBy'])],
            ],
            [
                $notStrictRules,
                new ArrayDataSet([]),
                [new Error('Value not passed.', [], ['orderBy'])],
            ],

            [
                [
                    'name' => [new Required(), new HasLength(min: 3, skipOnError: true)],
                    'description' => [new Required(), new HasLength(min: 5, skipOnError: true)],
                ],
                new ObjectDataSet(
                    new class () {
                        private string $title = '';
                        private string $description = 'abc123';
                    }
                ),
                [new Error('Value not passed.', [], ['name'])],
            ],
            [
                null,
                new ObjectDataSet(new ObjectWithDataSet()),
                [],
            ],
        ];
    }

    /**
     * @link https://github.com/yiisoft/validator/issues/173
     * @link https://github.com/yiisoft/validator/issues/289
     * @dataProvider requiredDataProvider
     */
    public function testRequired(array|null $rules, DataSetInterface $dataSet, array $expectedErrors): void
    {
        $validator = ValidatorFactory::make();
        $result = $validator->validate($dataSet, $rules);
        $this->assertEquals($expectedErrors, $result->getErrors());
    }

    public function skipOnEmptyDataProvider(): array
    {
        $translator = (new TranslatorFactory())->create();
        $validator = ValidatorFactory::make();
        $rules = [
            'name' => [new HasLength(min: 8)],
            'age' => [new Number(asInteger: true, min: 18)],
        ];
        $stringLessThanMinMessage = 'This value must contain at least 8 characters.';
        $incorrectNumberMessage = 'The allowed types are integer, float and string.';
        $intMessage = 'Value must be an integer.';
        $intLessThanMinMessage = 'Value must be no less than 18.';

        return [
            'rule / validator, skipOnEmpty: false, value not passed' => [
                $validator,
                new ArrayDataSet([
                    'name' => 'Dmitriy',
                ]),
                $rules,
                [
                    new Error($stringLessThanMinMessage, [
                        'min' => 8,
                        'attribute' => 'name',
                        'number' => 7,
                    ], ['name']),
                    new Error($incorrectNumberMessage, [
                        'attribute' => 'age',
                        'type' => 'null',
                    ], ['age']),
                ],
            ],
            'rule / validator, skipOnEmpty: false, value is empty' => [
                $validator,
                new ArrayDataSet([
                    'name' => 'Dmitriy',
                    'age' => null,
                ]),
                $rules,
                [
                    new Error($stringLessThanMinMessage, [
                        'min' => 8,
                        'attribute' => 'name',
                        'number' => 7,
                    ], ['name']),
                    new Error($incorrectNumberMessage, [
                        'attribute' => 'age',
                        'type' => 'null',
                    ], ['age']),
                ],
            ],
            'rule / validator, skipOnEmpty: false, value is not empty' => [
                $validator,
                new ArrayDataSet([
                    'name' => 'Dmitriy',
                    'age' => 17,
                ]),
                $rules,
                [
                    new Error($stringLessThanMinMessage, [
                        'min' => 8,
                        'attribute' => 'name',
                        'number' => 7,
                    ], ['name']),
                    new Error($intLessThanMinMessage, [
                        'min' => 18,
                        'attribute' => 'age',
                        'value' => 17,
                    ], ['age']),
                ],
            ],

            'rule, skipOnEmpty: true, value not passed' => [
                $validator,
                new ArrayDataSet([
                    'name' => 'Dmitriy',
                ]),
                [
                    'name' => [new HasLength(min: 8)],
                    'age' => [new Number(asInteger: true, min: 18, skipOnEmpty: true)],
                ],
                [
                    new Error($stringLessThanMinMessage, [
                        'min' => 8,
                        'attribute' => 'name',
                        'number' => 7,
                    ], ['name']),
                ],
            ],
            'rule, skipOnEmpty: true, value is empty (null)' => [
                $validator,
                new ArrayDataSet([
                    'name' => 'Dmitriy',
                    'age' => null,
                ]),
                [
                    'name' => [new HasLength(min: 8)],
                    'age' => [new Number(asInteger: true, min: 18, skipOnEmpty: true)],
                ],
                [
                    new Error($stringLessThanMinMessage, [
                        'min' => 8,
                        'attribute' => 'name',
                        'number' => 7,
                    ], ['name']),
                ],
            ],
            'rule, skipOnEmpty: true, value is empty (empty string after trimming), trimString is false' => [
                $validator,
                new ArrayDataSet([
                    'name' => ' ',
                    'age' => 17,
                ]),
                [
                    'name' => [new HasLength(min: 8, skipOnEmpty: true)],
                    'age' => [new Number(asInteger: true, min: 18)],
                ],
                [
                    new Error($stringLessThanMinMessage, [
                        'min' => 8,
                        'attribute' => 'name',
                        'number' => 1,
                    ], ['name']),
                    new Error($intLessThanMinMessage, [
                        'min' => 18,
                        'attribute' => 'age',
                        'value' => 17,
                    ], ['age']),
                ],
            ],
            'rule, skipOnEmpty: SkipOnEmpty, value is empty (empty string after trimming), trimString is true' => [
                $validator,
                new ArrayDataSet([
                    'name' => ' ',
                    'age' => 17,
                ]),
                [
                    'name' => [new HasLength(min: 8, skipOnEmpty: new WhenEmpty(trimString: true))],
                    'age' => [new Number(asInteger: true, min: 18)],
                ],
                [
                    new Error($intLessThanMinMessage, [
                        'min' => 18,
                        'attribute' => 'age',
                        'value' => 17,
                    ], ['age']),
                ],
            ],
            'rule, skipOnEmpty: true, value is not empty' => [
                $validator,
                new ArrayDataSet([
                    'name' => 'Dmitriy',
                    'age' => 17,
                ]),
                [
                    'name' => [new HasLength(min: 8)],
                    'age' => [new Number(asInteger: true, min: 18, skipOnEmpty: true)],
                ],
                [
                    new Error($stringLessThanMinMessage, [
                        'min' => 8,
                        'attribute' => 'name',
                        'number' => 7,
                    ], ['name']),
                    new Error($intLessThanMinMessage, [
                        'min' => 18,
                        'attribute' => 'age',
                        'value' => 17,
                    ], ['age']),
                ],
            ],

            'rule, skipOnEmpty: SkipOnNull, value not passed' => [
                $validator,
                new ArrayDataSet([
                    'name' => 'Dmitriy',
                ]),
                [
                    'name' => [new HasLength(min: 8)],
                    'age' => [new Number(asInteger: true, min: 18, skipOnEmpty: new WhenNull())],
                ],
                [
                    new Error($stringLessThanMinMessage, [
                        'min' => 8,
                        'attribute' => 'name',
                        'number' => 7,
                    ], ['name']),
                ],
            ],
            'rule, skipOnEmpty: SkipOnNull, value is empty' => [
                $validator,
                new ArrayDataSet([
                    'name' => 'Dmitriy',
                    'age' => null,
                ]),
                [
                    'name' => [new HasLength(min: 8)],
                    'age' => [new Number(asInteger: true, min: 18, skipOnEmpty: new WhenNull())],
                ],
                [
                    new Error($stringLessThanMinMessage, [
                        'min' => 8,
                        'attribute' => 'name',
                        'number' => 7,
                    ], ['name']),
                ],
            ],
            'rule, skipOnEmpty: SkipOnNull, value is not empty' => [
                $validator,
                new ArrayDataSet([
                    'name' => 'Dmitriy',
                    'age' => 17,
                ]),
                [
                    'name' => [new HasLength(min: 8)],
                    'age' => [new Number(asInteger: true, min: 18, skipOnEmpty: new WhenNull())],
                ],
                [
                    new Error($stringLessThanMinMessage, [
                        'min' => 8,
                        'attribute' => 'name',
                        'number' => 7,
                    ], ['name']),
                    new Error($intLessThanMinMessage, [
                        'min' => 18,
                        'attribute' => 'age',
                        'value' => 17,
                    ], ['age']),
                ],
            ],
            'rule, skipOnEmpty: SkipOnNull, value is not empty (empty string)' => [
                $validator,
                new ArrayDataSet([
                    'name' => 'Dmitriy',
                    'age' => '',
                ]),
                [
                    'name' => [new HasLength(min: 8)],
                    'age' => [new Number(asInteger: true, min: 18, skipOnEmpty: new WhenNull())],
                ],
                [
                    new Error($stringLessThanMinMessage, [
                        'min' => 8,
                        'attribute' => 'name',
                        'number' => 7,
                    ], ['name']),
                    new Error($intMessage, [
                        'attribute' => 'age',
                        'value' => '',
                    ], ['age']),
                ],
            ],

            'rule, skipOnEmpty: custom callback, value not passed' => [
                $validator,
                new ArrayDataSet([
                    'name' => 'Dmitriy',
                ]),
                [
                    'name' => [new HasLength(min: 8)],
                    'age' => [
                        new Number(
                            asInteger: true,
                            min: 18,
                            skipOnEmpty: static fn (mixed $value, bool $isAttributeMissing): bool => $value === 0
                        ),
                    ],
                ],
                [
                    new Error($stringLessThanMinMessage, [
                        'min' => 8,
                        'attribute' => 'name',
                        'number' => 7,
                    ], ['name']),
                    new Error($incorrectNumberMessage, [
                        'attribute' => 'age',
                        'type' => 'null',
                    ], ['age']),
                ],
            ],
            'rule, skipOnEmpty: custom callback, value is empty' => [
                $validator,
                new ArrayDataSet([
                    'name' => 'Dmitriy',
                    'age' => 0,
                ]),
                [
                    'name' => [new HasLength(min: 8)],
                    'age' => [
                        new Number(
                            asInteger: true,
                            min: 18,
                            skipOnEmpty: static fn (mixed $value, bool $isAttributeMissing): bool => $value === 0
                        ),
                    ],
                ],
                [
                    new Error($stringLessThanMinMessage, [
                        'min' => 8,
                        'attribute' => 'name',
                        'number' => 7,
                    ], ['name']),
                ],
            ],
            'rule, skipOnEmpty, custom callback, value is not empty' => [
                $validator,
                new ArrayDataSet([
                    'name' => 'Dmitriy',
                    'age' => 17,
                ]),
                [
                    'name' => [new HasLength(min: 8)],
                    'age' => [
                        new Number(
                            asInteger: true,
                            min: 18,
                            skipOnEmpty: static fn (mixed $value, bool $isAttributeMissing): bool => $value === 0
                        ),
                    ],
                ],
                [
                    new Error($stringLessThanMinMessage, [
                        'min' => 8,
                        'attribute' => 'name',
                        'number' => 7,
                    ], ['name']),
                    new Error($intLessThanMinMessage, [
                        'min' => 18,
                        'attribute' => 'age',
                        'value' => 17,
                    ], ['age']),
                ],
            ],
            'rule, skipOnEmpty, custom callback, value is not empty (null)' => [
                $validator,
                new ArrayDataSet([
                    'name' => 'Dmitriy',
                    'age' => null,
                ]),
                [
                    'name' => [new HasLength(min: 8)],
                    'age' => [
                        new Number(
                            asInteger: true,
                            min: 18,
                            skipOnEmpty: static fn (mixed $value, bool $isAttributeMissing): bool => $value === 0
                        ),
                    ],
                ],
                [
                    new Error($stringLessThanMinMessage, [
                        'min' => 8,
                        'attribute' => 'name',
                        'number' => 7,
                    ], ['name']),
                    new Error($incorrectNumberMessage, [
                        'attribute' => 'age',
                        'type' => 'null',
                    ], ['age']),
                ],
            ],

            'validator, skipOnEmpty: true, value not passed' => [
                new Validator(new SimpleRuleHandlerContainer(), $translator, defaultSkipOnEmpty: true),
                new ArrayDataSet([
                    'name' => 'Dmitriy',
                ]),
                $rules,
                [
                    new Error($stringLessThanMinMessage, [
                        'min' => 8,
                        'attribute' => 'name',
                        'number' => 7,
                    ], ['name']),
                ],
            ],
            'validator, skipOnEmpty: true, value is empty' => [
                new Validator(new SimpleRuleHandlerContainer(), $translator, defaultSkipOnEmpty: true),
                new ArrayDataSet([
                    'name' => 'Dmitriy',
                    'age' => null,
                ]),
                $rules,
                [
                    new Error($stringLessThanMinMessage, [
                        'min' => 8,
                        'attribute' => 'name',
                        'number' => 7,
                    ], ['name']),
                ],
            ],
            'validator, skipOnEmpty: true, value is not empty' => [
                new Validator(new SimpleRuleHandlerContainer(), $translator, defaultSkipOnEmpty: true),
                new ArrayDataSet([
                    'name' => 'Dmitriy',
                    'age' => 17,
                ]),
                $rules,
                [
                    new Error($stringLessThanMinMessage, [
                        'min' => 8,
                        'attribute' => 'name',
                        'number' => 7,
                    ], ['name']),
                    new Error($intLessThanMinMessage, [
                        'min' => 18,
                        'attribute' => 'age',
                        'value' => 17,
                    ], ['age']),
                ],
            ],

            'validator, skipOnEmpty: SkipOnNull, value not passed' => [
                new Validator(new SimpleRuleHandlerContainer(), $translator, defaultSkipOnEmpty: new WhenNull()),
                new ArrayDataSet([
                    'name' => 'Dmitriy',
                ]),
                $rules,
                [
                    new Error($stringLessThanMinMessage, [
                        'min' => 8,
                        'attribute' => 'name',
                        'number' => 7,
                    ], ['name']),
                ],
            ],
            'validator, skipOnEmpty: SkipOnNull, value is empty' => [
                new Validator(new SimpleRuleHandlerContainer(), $translator, defaultSkipOnEmpty: new WhenNull()),
                new ArrayDataSet([
                    'name' => 'Dmitriy',
                    'age' => null,
                ]),
                $rules,
                [
                    new Error($stringLessThanMinMessage, [
                        'min' => 8,
                        'attribute' => 'name',
                        'number' => 7,
                    ], ['name']),
                ],
            ],
            'validator, skipOnEmpty: SkipOnNull, value is not empty' => [
                new Validator(new SimpleRuleHandlerContainer(), $translator, defaultSkipOnEmpty: new WhenNull()),
                new ArrayDataSet([
                    'name' => 'Dmitriy',
                    'age' => 17,
                ]),
                $rules,
                [
                    new Error($stringLessThanMinMessage, [
                        'min' => 8,
                        'attribute' => 'name',
                        'number' => 7,
                    ], ['name']),
                    new Error($intLessThanMinMessage, [
                        'min' => 18,
                        'attribute' => 'age',
                        'value' => 17,
                    ], ['age']),
                ],
            ],
            'validator, skipOnEmpty: SkipOnNull, value is not empty (empty string)' => [
                new Validator(new SimpleRuleHandlerContainer(), $translator, defaultSkipOnEmpty: new WhenNull()),
                new ArrayDataSet([
                    'name' => 'Dmitriy',
                    'age' => '',
                ]),
                $rules,
                [
                    new Error($stringLessThanMinMessage, [
                        'min' => 8,
                        'attribute' => 'name',
                        'number' => 7,
                    ], ['name']),
                    new Error($intMessage, [
                        'attribute' => 'age',
                        'value' => '',
                    ], ['age']),
                ],
            ],

            'validator, skipOnEmpty: custom callback, value not passed' => [
                new Validator(
                    new SimpleRuleHandlerContainer(),
                    $translator,
                    defaultSkipOnEmpty: static fn (mixed $value, bool $isAttributeMissing): bool => $value === 0
                ),
                new ArrayDataSet([
                    'name' => 'Dmitriy',
                ]),
                $rules,
                [
                    new Error($stringLessThanMinMessage, [
                        'min' => 8,
                        'attribute' => 'name',
                        'number' => 7,
                    ], ['name']),
                    new Error($incorrectNumberMessage, [
                        'attribute' => 'age',
                        'type' => 'null',
                    ], ['age']),
                ],
            ],
            'validator, skipOnEmpty: custom callback, value is empty' => [
                new Validator(
                    new SimpleRuleHandlerContainer(),
                    $translator,
                    defaultSkipOnEmpty: static fn (mixed $value, bool $isAttributeMissing): bool => $value === 0
                ),
                new ArrayDataSet([
                    'name' => 'Dmitriy',
                    'age' => 0,
                ]),
                $rules,
                [
                    new Error($stringLessThanMinMessage, [
                        'min' => 8,
                        'attribute' => 'name',
                        'number' => 7,
                    ], ['name']),
                ],
            ],
            'validator, skipOnEmpty: custom callback, value is not empty' => [
                new Validator(
                    new SimpleRuleHandlerContainer(),
                    $translator,
                    defaultSkipOnEmpty: static fn (mixed $value, bool $isAttributeMissing): bool => $value === 0
                ),
                new ArrayDataSet([
                    'name' => 'Dmitriy',
                    'age' => 17,
                ]),
                $rules,
                [
                    new Error($stringLessThanMinMessage, [
                        'min' => 8,
                        'attribute' => 'name',
                        'number' => 7,
                    ], ['name']),
                    new Error($intLessThanMinMessage, [
                        'min' => 18,
                        'attribute' => 'age',
                        'value' => 17,
                    ], ['age']),
                ],
            ],
            'validator, skipOnEmpty: custom callback, value is not empty (null)' => [
                new Validator(
                    new SimpleRuleHandlerContainer(),
                    $translator,
                    defaultSkipOnEmpty: static fn (mixed $value, bool $isAttributeMissing): bool => $value === 0
                ),
                new ArrayDataSet([
                    'name' => 'Dmitriy',
                    'age' => null,
                ]),
                $rules,
                [
                    new Error($stringLessThanMinMessage, [
                        'min' => 8,
                        'attribute' => 'name',
                        'number' => 7,
                    ], ['name']),
                    new Error($incorrectNumberMessage, [
                        'attribute' => 'age',
                        'type' => 'null',
                    ], ['age']),
                ],
            ],
        ];
    }

    /**
     * @param StubRule[] $rules
     * @param Error[] $expectedErrors
     *
     * @dataProvider skipOnEmptyDataProvider
     */
    public function testSkipOnEmpty(Validator $validator, ArrayDataSet $data, array $rules, array $expectedErrors): void
    {
        $result = $validator->validate($data, $rules);
        $this->assertEquals($expectedErrors, $result->getErrors());
    }

    public function initSkipOnEmptyDataProvider(): array
    {
        return [
            'null' => [
                null,
                new class () {
                    #[Number]
                    public ?string $name = null;
                },
                false,
            ],
            'false' => [
                false,
                new class () {
                    #[Number]
                    public ?string $name = null;
                },
                false,
            ],
            'true' => [
                true,
                new class () {
                    #[Number]
                    public ?string $name = null;
                },
                true,
            ],
            'callable' => [
                new WhenNull(),
                new class () {
                    #[Number]
                    public ?string $name = null;
                },
                true,
            ],
            'do-not-override-rule' => [
                false,
                new class () {
                    #[Number(skipOnEmpty: true)]
                    public string $name = '';
                },
                true,
            ],
        ];
    }

    /**
     * @dataProvider initSkipOnEmptyDataProvider
     */
    public function testInitSkipOnEmpty(
        bool|callable|null $skipOnEmpty,
        mixed $data,
        bool $expectedResult,
    ): void {
        $translator = (new TranslatorFactory())->create();
        $validator = new Validator(new SimpleRuleHandlerContainer(), $translator, defaultSkipOnEmpty: $skipOnEmpty);

        $result = $validator->validate($data);

        $this->assertSame($expectedResult, $result->isValid());
    }

    public function testObjectWithAttributesOnly(): void
    {
        $object = new ObjectWithAttributesOnly();

        $validator = ValidatorFactory::make();

        $result = $validator->validate($object);

        $this->assertFalse($result->isValid());
        $this->assertCount(1, $result->getErrorMessages());
        $this->assertStringStartsWith('This value must contain at least', $result->getErrorMessages()[0]);
    }

    public function testRuleWithoutSkipOnEmpty(): void
    {
        $translator = (new TranslatorFactory())->create();
        $validator = new Validator(new SimpleRuleHandlerContainer(), $translator, defaultSkipOnEmpty: new WhenNull());

        $data = new class () {
            #[NotNull]
            public ?string $name = null;
        };

        $result = $validator->validate($data);

        $this->assertFalse($result->isValid());
    }

    public function testValidateWithSingleRule(): void
    {
        $result = ValidatorFactory::make()->validate(3, new Number(min: 5));

        $this->assertFalse($result->isValid());
        $this->assertSame(
            ['' => ['Value must be no less than 5.']],
            $result->getErrorMessagesIndexedByPath(),
        );
    }

    public function testComposition(): void
    {
        $validator = new class () implements ValidatorInterface {
            private Validator $validator;

            public function __construct()
            {
                $this->validator = new Validator(
                    new SimpleRuleHandlerContainer(),
                    (new TranslatorFactory())->create(),
                );
            }

            public function validate(
                mixed $data,
                iterable|object|string|null $rules = null,
                ?ValidationContext $context = null
            ): Result {
                $dataSet = DataSetHelper::normalize($data);
                $context ??= new ValidationContext($this, $dataSet);

                $result = $this->validator->validate($data, $rules, $context);

                return $context->getParameter('forceSuccess') === true ? new Result() : $result;
            }
        };

        $rules = [
            static function ($value, $rule, ValidationContext $context) {
                $context->setParameter('forceSuccess', true);
                return (new Result())->addError('fail');
            },
        ];

        $result = $validator->validate([], $rules);

        $this->assertTrue($result->isValid());
    }

    public function testRulesWithWrongKey(): void
    {
        $validator = ValidatorFactory::make();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('An attribute can only have an integer or a string type. bool given.');
        $validator->validate([], new IteratorWithBooleanKey());
    }

    public function testRulesWithWrongRule(): void
    {
        $validator = ValidatorFactory::make();

        $this->expectException(InvalidArgumentException::class);
        $message = 'Rule should be either an instance of Yiisoft\Validator\RuleInterface or a callable, int given.';
        $this->expectExceptionMessage($message);
        $validator->validate([], [new Boolean(), 1]);
    }

    public function testRulesAsObjectNameWithRuleAttributes(): void
    {
        $validator = ValidatorFactory::make();
        $result = $validator->validate(['name' => 'Test name'], ObjectWithAttributesOnly::class);
        $this->assertTrue($result->isValid());
    }

    public function testRulesAsObjectWithRuleAttributes(): void
    {
        $validator = ValidatorFactory::make();
        $result = $validator->validate(['name' => 'Test name'], new ObjectWithAttributesOnly());
        $this->assertTrue($result->isValid());
    }

    public function testDataWithPostValidationHook(): void
    {
        $validator = ValidatorFactory::make();
        $this->assertFalse(ObjectWithPostValidationHook::$hookCalled);

        $result = $validator->validate(new ObjectWithPostValidationHook(), ['called' => new Boolean()]);
        $this->assertFalse($result->isValid());
        $this->assertTrue(ObjectWithPostValidationHook::$hookCalled);
    }

    public function testSkippingRuleInPreValidate(): void
    {
        $data = ['agree' => false, 'viewsCount' => -1];
        $rules = [
            'agree' => [new Boolean(skipOnEmpty: static fn (): bool => true), new IsTrue()],
            'viewsCount' => [new Number(asInteger: true, min: 0)],
        ];
        $validator = ValidatorFactory::make();

        $result = $validator->validate($data, $rules);
        $this->assertSame(
            [
                'agree' => ['The value must be "1".'],
                'viewsCount' => ['Value must be no less than 0.'],
            ],
            $result->getErrorMessagesIndexedByPath(),
        );
    }

    public function testDefaultTranslatorWithIntl(): void
    {
        if (!extension_loaded('intl')) {
            $this->markTestSkipped('The intl extension must be available for this test.');
        }

        $this->checkDefaultTranslator('3-few');
    }

    public function testDefaultTranslatorWithoutIntl(): void
    {
        if (extension_loaded('intl')) {
            $this->markTestSkipped('The intl extension must be unavailable for this test.');
        }

        $this->checkDefaultTranslator('3');
    }

    private function checkDefaultTranslator(string $expectedErrorMessage): void
    {
        $data = ['number' => 3];
        $rules = [
            'number' => new Number(
                asInteger: true,
                max: 2,
                tooBigMessage: '{value, selectordinal, one{#-one} two{#-two} few{#-few} other{#-other}}',
            ),
        ];
        $validator = new Validator(new SimpleRuleHandlerContainer());

        $result = $validator->validate($data, $rules);
        $this->assertSame(['number' => [$expectedErrorMessage]], $result->getErrorMessagesIndexedByPath());
    }
}
