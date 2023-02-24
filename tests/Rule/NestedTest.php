<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use ArrayObject;
use InvalidArgumentException;
use ReflectionProperty;
use stdClass;
use Yiisoft\Validator\DataSet\ObjectDataSet;
use Yiisoft\Validator\DataSetInterface;
use Yiisoft\Validator\Error;
use Yiisoft\Validator\Helper\RulesDumper;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\AtLeast;
use Yiisoft\Validator\Rule\BooleanValue;
use Yiisoft\Validator\Rule\Callback;
use Yiisoft\Validator\Rule\Count;
use Yiisoft\Validator\Rule\Each;
use Yiisoft\Validator\Rule\Integer;
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\Rule\In;
use Yiisoft\Validator\Rule\Nested;
use Yiisoft\Validator\Rule\NestedHandler;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Rule\Regex;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\RuleInterface;
use Yiisoft\Validator\RulesProviderInterface;
use Yiisoft\Validator\Tests\Rule\Base\DifferentRuleInHandlerTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\RuleTestCase;
use Yiisoft\Validator\Tests\Rule\Base\RuleWithOptionsTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\SkipOnErrorTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\WhenTestTrait;
use Yiisoft\Validator\Tests\Support\Data\EachNestedObjects\Foo;
use Yiisoft\Validator\Tests\Support\Data\IteratorWithBooleanKey;
use Yiisoft\Validator\Tests\Support\Data\InheritAttributesObject\InheritAttributesObject;
use Yiisoft\Validator\Tests\Support\Data\ObjectWithDifferentPropertyVisibility;
use Yiisoft\Validator\Tests\Support\Data\ObjectWithNestedObject;
use Yiisoft\Validator\Tests\Support\Helper\OptionsHelper;
use Yiisoft\Validator\Tests\Support\Rule\StubRule\StubRuleWithOptions;
use Yiisoft\Validator\Tests\Support\RulesProvider\SimpleRulesProvider;
use Yiisoft\Validator\ValidationContext;
use Yiisoft\Validator\Validator;

use function array_slice;

final class NestedTest extends RuleTestCase
{
    use DifferentRuleInHandlerTestTrait;
    use RuleWithOptionsTestTrait;
    use SkipOnErrorTestTrait;
    use WhenTestTrait;

    public function testGetName(): void
    {
        $rule = new Nested();

        $this->assertSame('nested', $rule->getName());
    }

    public function testDefaultValues(): void
    {
        $rule = new Nested();

        $this->assertNull($rule->getRules());
        $this->assertSame(
            ReflectionProperty::IS_PRIVATE | ReflectionProperty::IS_PROTECTED | ReflectionProperty::IS_PUBLIC,
            $rule->getValidatedObjectPropertyVisibility(),
        );
        $this->assertFalse($rule->isPropertyPathRequired());
        $this->assertSame('Property "{path}" is not found.', $rule->getNoPropertyPathMessage());
        $this->assertNull($rule->getSkipOnEmpty());
        $this->assertFalse($rule->shouldSkipOnError());
        $this->assertNull($rule->getWhen());
    }

    public function testPropertyVisibilityInConstructor(): void
    {
        $rule = new Nested(validatedObjectPropertyVisibility: ReflectionProperty::IS_PRIVATE);

        $this->assertSame(ReflectionProperty::IS_PRIVATE, $rule->getValidatedObjectPropertyVisibility());
    }

    public function testHandlerClassName(): void
    {
        $rule = new Nested();

        $this->assertSame(NestedHandler::class, $rule->getHandler());
    }

    public function dataOptions(): array
    {
        return [
            [
                new Nested([new Number(pattern: '/1/')]),
                [
                    'noRulesWithNoObjectMessage' => [
                        'template' => 'Nested rule without rules can be used for objects only.',
                        'parameters' => [],
                    ],
                    'incorrectDataSetTypeMessage' => [
                        'template' => 'An object data set data can only have an array type.',
                        'parameters' => [],
                    ],
                    'incorrectInputMessage' => [
                        'template' => 'The value must be an array or an object.',
                        'parameters' => [],
                    ],
                    'noPropertyPathMessage' => [
                        'template' => 'Property "{path}" is not found.',
                        'parameters' => [],
                    ],
                    'requirePropertyPath' => false,
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                    'rules' => [
                        [
                            'number',
                            'min' => null,
                            'max' => null,
                            'incorrectInputMessage' => [
                                'template' => 'The allowed types are integer, float and string.',
                                'parameters' => [],
                            ],
                            'notNumberMessage' => [
                                'template' => 'Value must be a number.',
                                'parameters' => [],
                            ],
                            'lessThanMinMessage' => [
                                'template' => 'Value must be no less than {min}.',
                                'parameters' => ['min' => null],
                            ],
                            'greaterThanMaxMessage' => [
                                'template' => 'Value must be no greater than {max}.',
                                'parameters' => ['max' => null],
                            ],
                            'skipOnEmpty' => false,
                            'skipOnError' => false,
                            'pattern' => '/1/',
                        ],
                    ],
                ],
            ],
            [
                new Nested(['user.age' => new Number(pattern: '/1/')]),
                [
                    'noRulesWithNoObjectMessage' => [
                        'template' => 'Nested rule without rules can be used for objects only.',
                        'parameters' => [],
                    ],
                    'incorrectDataSetTypeMessage' => [
                        'template' => 'An object data set data can only have an array type.',
                        'parameters' => [],
                    ],
                    'incorrectInputMessage' => [
                        'template' => 'The value must be an array or an object.',
                        'parameters' => [],
                    ],
                    'noPropertyPathMessage' => [
                        'template' => 'Property "{path}" is not found.',
                        'parameters' => [],
                    ],
                    'requirePropertyPath' => false,
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                    'rules' => [
                        'user.age' => [
                            [
                                'number',
                                'min' => null,
                                'max' => null,
                                'incorrectInputMessage' => [
                                    'template' => 'The allowed types are integer, float and string.',
                                    'parameters' => [],
                                ],
                                'notNumberMessage' => [
                                    'template' => 'Value must be a number.',
                                    'parameters' => [],
                                ],
                                'lessThanMinMessage' => [
                                    'template' => 'Value must be no less than {min}.',
                                    'parameters' => ['min' => null],
                                ],
                                'greaterThanMaxMessage' => [
                                    'template' => 'Value must be no greater than {max}.',
                                    'parameters' => ['max' => null],
                                ],
                                'skipOnEmpty' => false,
                                'skipOnError' => false,
                                'pattern' => '/1/',
                            ],
                        ],
                    ],
                ],
            ],
            [
                new Nested([
                    'author.name' => new StubRuleWithOptions('author-name', ['key' => 'name']),
                    'author.age' => new StubRuleWithOptions('author-age', ['key' => 'age']),
                ]),
                [
                    'noRulesWithNoObjectMessage' => [
                        'template' => 'Nested rule without rules can be used for objects only.',
                        'parameters' => [],
                    ],
                    'incorrectDataSetTypeMessage' => [
                        'template' => 'An object data set data can only have an array type.',
                        'parameters' => [],
                    ],
                    'incorrectInputMessage' => [
                        'template' => 'The value must be an array or an object.',
                        'parameters' => [],
                    ],
                    'noPropertyPathMessage' => [
                        'template' => 'Property "{path}" is not found.',
                        'parameters' => [],
                    ],
                    'requirePropertyPath' => false,
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                    'rules' => [
                        'author.name' => [['author-name', 'key' => 'name']],
                        'author.age' => [['author-age', 'key' => 'age']],
                    ],
                ],
            ],
            [
                new Nested([
                    'author' => [
                        'name' => new StubRuleWithOptions('author-name', ['key' => 'name']),
                        'age' => new StubRuleWithOptions('author-age', ['key' => 'age']),
                    ],
                ]),
                [
                    'noRulesWithNoObjectMessage' => [
                        'template' => 'Nested rule without rules can be used for objects only.',
                        'parameters' => [],
                    ],
                    'incorrectDataSetTypeMessage' => [
                        'template' => 'An object data set data can only have an array type.',
                        'parameters' => [],
                    ],
                    'incorrectInputMessage' => [
                        'template' => 'The value must be an array or an object.',
                        'parameters' => [],
                    ],
                    'noPropertyPathMessage' => [
                        'template' => 'Property "{path}" is not found.',
                        'parameters' => [],
                    ],
                    'requirePropertyPath' => false,
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                    'rules' => [
                        'author.name' => [['author-name', 'key' => 'name']],
                        'author.age' => [['author-age', 'key' => 'age']],
                    ],
                ],
            ],
        ];
    }

    public function testGetOptionsWithNotRule(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $ruleInterfaceName = RuleInterface::class;
        $message = "Every rule must be an instance of $ruleInterfaceName, class@anonymous given.";
        $this->expectExceptionMessage($message);

        $rule = new Nested([
            'a' => new Required(),
            'b' => new class () {
            },
            'c' => new Number(min: 1),
        ]);
        $rule->getOptions();
    }

    public function testValidationRuleIsNotInstanceOfRule(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Nested(['path.to.value' => (new stdClass())]);
    }

    public function testWithNestedAndEachShortcutBare(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Bare shortcut is prohibited. Use "Each" rule instead.');
        new Nested(['*' => [new Number(min: -10, max: 10)]]);
    }

    public function dataHandler(): array
    {
        return [
            'class-string-rules' => [
                new class () {
                    #[Nested(ObjectWithDifferentPropertyVisibility::class)]
                    private array $array = [
                        'name' => 'hello',
                        'age' => 17,
                        'number' => 500,
                    ];
                },
                [
                    'array.age' => ['Value must be no less than 21.'],
                    'array.number' => ['Value must be no greater than 100.'],
                ],
            ],
            'class-string-rules-private-only' => [
                new class () {
                    #[Nested(
                        rules: ObjectWithDifferentPropertyVisibility::class,
                        rulesSourceClassPropertyVisibility: ReflectionProperty::IS_PRIVATE,
                    )]
                    private array $array = [
                        'name' => 'hello',
                        'age' => 17,
                        'number' => 500,
                    ];
                },
                [
                    'array.number' => ['Value must be no greater than 100.'],
                ],
            ],
            'rules-provider' => [
                new class () implements RulesProviderInterface {
                    private array $array = [
                        'name' => 'hello',
                        'age' => 17,
                        'number' => 500,
                    ];

                    public function getRules(): iterable
                    {
                        return [
                            'array' => new Nested(
                                new SimpleRulesProvider([
                                    'age' => new Number(min: 99),
                                ])
                            ),
                        ];
                    }
                },
                [
                    'array.age' => ['Value must be no less than 99.'],
                ],
            ],
            'empty-rules' => [
                new class () {
                    #[Nested([])]
                    private ObjectWithDifferentPropertyVisibility $object;

                    public function __construct()
                    {
                        $this->object = new ObjectWithDifferentPropertyVisibility();
                    }
                },
                [],
            ],
            'rules-from-validated-value' => [
                new class () {
                    #[Nested]
                    private ObjectWithDifferentPropertyVisibility $object;

                    public function __construct()
                    {
                        $this->object = new ObjectWithDifferentPropertyVisibility();
                    }
                },
                [
                    'object.name' => ['Value cannot be blank.'],
                    'object.age' => ['Value must be no less than 21.'],
                ],
            ],
            'rules-from-validated-value-only-public' => [
                new class () {
                    #[Nested(validatedObjectPropertyVisibility: ReflectionProperty::IS_PUBLIC)]
                    private ObjectWithDifferentPropertyVisibility $object;

                    public function __construct()
                    {
                        $this->object = new ObjectWithDifferentPropertyVisibility();
                    }
                },
                [
                    'object.name' => ['Value cannot be blank.'],
                ],
            ],
            'rules-from-validated-value-only-protected' => [
                new class () {
                    #[Nested(validatedObjectPropertyVisibility: ReflectionProperty::IS_PROTECTED)]
                    private ObjectWithDifferentPropertyVisibility $object;

                    public function __construct()
                    {
                        $this->object = new ObjectWithDifferentPropertyVisibility();
                    }
                },
                [
                    'object.age' => ['Value must be no less than 21.'],
                ],
            ],
            'rules-from-validated-value-inherit-attributes' => [
                new class () {
                    #[Nested]
                    private InheritAttributesObject $object;

                    public function __construct()
                    {
                        $this->object = new InheritAttributesObject();
                    }
                },
                [
                    'object.age' => [
                        'Value must be no less than 21.',
                        'Value must be equal to "23".',
                    ],
                    'object.number' => ['Value must be equal to "99".'],
                ],
            ],
            'nested-with-each' => [
                new Foo(),
                [
                    'name' => ['Value cannot be blank.'],
                    'bars.0.name' => ['Value cannot be blank.'],
                ],
            ],
        ];
    }

    /**
     * @dataProvider dataHandler
     */
    public function testHandler(object $data, array $expectedErrorMessagesIndexedByPath): void
    {
        $result = (new Validator())->validate($data);
        $this->assertSame($expectedErrorMessagesIndexedByPath, $result->getErrorMessagesIndexedByPath());
    }

    public function dataPropagateOptions(): array
    {
        return [
            'nested and each combinations' => [
                new Nested(
                    [
                        'posts' => [
                            new Each([
                                new Nested([
                                    'title' => [new Length(min: 3)],
                                    'authors' => [
                                        new Each([
                                            new Nested([
                                                'data' => [
                                                    'name' => [new Length(min: 5)],
                                                    'age' => [
                                                        new Number(min: 18),
                                                        new Number(min: 20),
                                                    ],
                                                ],
                                            ]),
                                        ]),
                                    ],
                                ]),
                            ]),
                        ],
                        'meta' => [new Length(min: 7)],
                    ],
                    propagateOptions: true,
                    skipOnEmpty: true,
                    skipOnError: true,
                ),
                [
                    [
                        'nested',
                        'skipOnEmpty' => true,
                        'skipOnError' => true,
                        'rules' => [
                            'posts' => [
                                [
                                    'each',
                                    'skipOnEmpty' => true,
                                    'skipOnError' => true,
                                    'rules' => [
                                        [
                                            [
                                                'nested',
                                                'skipOnEmpty' => true,
                                                'skipOnError' => true,
                                                'rules' => [
                                                    'title' => [
                                                        [
                                                            'length',
                                                            'skipOnEmpty' => true,
                                                            'skipOnError' => true,
                                                        ],
                                                    ],
                                                    'authors' => [
                                                        [
                                                            'each',
                                                            'skipOnEmpty' => true,
                                                            'skipOnError' => true,
                                                            'rules' => [
                                                                [
                                                                    [
                                                                        'nested',
                                                                        'skipOnEmpty' => true,
                                                                        'skipOnError' => true,
                                                                        'rules' => [
                                                                            'data.name' => [
                                                                                [
                                                                                    'length',
                                                                                    'skipOnEmpty' => true,
                                                                                    'skipOnError' => true,
                                                                                ],
                                                                            ],
                                                                            'data.age' => [
                                                                                [
                                                                                    'number',
                                                                                    'skipOnEmpty' => true,
                                                                                    'skipOnError' => true,
                                                                                ],
                                                                                [
                                                                                    'number',
                                                                                    'skipOnEmpty' => true,
                                                                                    'skipOnError' => true,
                                                                                ],
                                                                            ],
                                                                        ],
                                                                    ],
                                                                ],
                                                            ],
                                                        ],
                                                    ],
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            'meta' => [
                                [
                                    'length',
                                    'skipOnEmpty' => true,
                                    'skipOnError' => true,
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'null as rules' => [
                new Nested(propagateOptions: true),
                [
                    [
                        'nested',
                        'skipOnEmpty' => false,
                        'skipOnError' => false,
                        'rules' => null,
                    ],
                ],
            ],
            'single rule as integer attribute rules' => [
                new Nested(
                    [new AtLeast(['a'])],
                    propagateOptions: true,
                    skipOnEmpty: true,
                    skipOnError: true,
                ),
                [
                    [
                        'nested',
                        'skipOnEmpty' => true,
                        'skipOnError' => true,
                        'rules' => [
                            [
                                'atLeast',
                                'skipOnEmpty' => true,
                                'skipOnError' => true,
                            ],
                        ],
                    ],
                ],
            ],
            'single rule as string attribute rules' => [
                new Nested(
                    [
                        'numbers' => new Each(new Number()),
                    ],
                    propagateOptions: true,
                    skipOnEmpty: true,
                    skipOnError: true,
                ),
                [
                    [
                        'nested',
                        'skipOnEmpty' => true,
                        'skipOnError' => true,
                        'rules' => [
                            'numbers' => [
                                [
                                    'each',
                                    'skipOnEmpty' => true,
                                    'skipOnError' => true,
                                    'rules' => [
                                        [
                                            [
                                                'number',
                                                'skipOnEmpty' => true,
                                                'skipOnError' => true,
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @dataProvider dataPropagateOptions
     */
    public function testPropagateOptions(Nested $rule, array $expectedOptions): void
    {
        $options = RulesDumper::asArray([$rule]);
        OptionsHelper::filterRecursive($options, ['skipOnEmpty', 'skipOnError', 'rules']);
        $this->assertSame($expectedOptions, $options);
    }

    public function testNestedWithoutRulesWithObject(): void
    {
        $validator = new Validator();
        $result = $validator->validate(new ObjectWithNestedObject());

        $this->assertFalse($result->isValid());
        $this->assertSame(
            [
                'caption' => ['This value must contain at least 3 characters.'],
                'object.name' => ['This value must contain at least 5 characters.'],
            ],
            $result->getErrorMessagesIndexedByPath()
        );
    }

    public function dataWithOtherNestedAndEach(): array
    {
        $data = [
            'charts' => [
                [
                    'points' => [
                        ['coordinates' => ['x' => -11, 'y' => 11], 'rgb' => [-1, 256, 0]],
                        ['coordinates' => ['x' => -12, 'y' => 12], 'rgb' => [0, -2, 257]],
                    ],
                ],
                [
                    'points' => [
                        ['coordinates' => ['x' => -1, 'y' => 1], 'rgb' => [0, 0, 0]],
                        ['coordinates' => ['x' => -2, 'y' => 2], 'rgb' => [255, 255, 255]],
                    ],
                ],
                [
                    'points' => [
                        ['coordinates' => ['x' => -13, 'y' => 13], 'rgb' => [-3, 258, 0]],
                        ['coordinates' => ['x' => -14, 'y' => 14], 'rgb' => [0, -4, 259]],
                    ],
                ],
            ],
        ];
        $xRules = [
            new Number(min: -10, max: 10),
            new Callback(static function (mixed $value, object $rule, ValidationContext $context): Result {
                $result = new Result();
                $result->addError('Custom error.');

                return $result;
            }),
        ];
        $yRules = [new Number(min: -10, max: 10)];
        $rgbRules = [
            new Count(3),
            new Each([new Number(min: 0, max: 255)]),
        ];

        $detailedErrorsData = [
            [['charts', 0, 'points', 0, 'coordinates', 'x'], 'Value must be no less than -10.'],
            [['charts', 0, 'points', 0, 'coordinates', 'x'], 'Custom error.'],
            [['charts', 0, 'points', 0, 'coordinates', 'y'], 'Value must be no greater than 10.'],
            [['charts', 0, 'points', 0, 'rgb', 0], 'Value must be no less than 0.'],
            [['charts', 0, 'points', 0, 'rgb', 1], 'Value must be no greater than 255.'],
            [['charts', 0, 'points', 1, 'coordinates', 'x'], 'Value must be no less than -10.'],
            [['charts', 0, 'points', 1, 'coordinates', 'x'], 'Custom error.'],
            [['charts', 0, 'points', 1, 'coordinates', 'y'], 'Value must be no greater than 10.'],
            [['charts', 0, 'points', 1, 'rgb', 1], 'Value must be no less than 0.'],
            [['charts', 0, 'points', 1, 'rgb', 2], 'Value must be no greater than 255.'],
            [['charts', 1, 'points', 0, 'coordinates', 'x'], 'Custom error.'],
            [['charts', 1, 'points', 1, 'coordinates', 'x'], 'Custom error.'],
            [['charts', 2, 'points', 0, 'coordinates', 'x'], 'Value must be no less than -10.'],
            [['charts', 2, 'points', 0, 'coordinates', 'x'], 'Custom error.'],
            [['charts', 2, 'points', 0, 'coordinates', 'y'], 'Value must be no greater than 10.'],
            [['charts', 2, 'points', 0, 'rgb', 0], 'Value must be no less than 0.'],
            [['charts', 2, 'points', 0, 'rgb', 1], 'Value must be no greater than 255.'],
            [['charts', 2, 'points', 1, 'coordinates', 'x'], 'Value must be no less than -10.'],
            [['charts', 2, 'points', 1, 'coordinates', 'x'], 'Custom error.'],
            [['charts', 2, 'points', 1, 'coordinates', 'y'], 'Value must be no greater than 10.'],
            [['charts', 2, 'points', 1, 'rgb', 1], 'Value must be no less than 0.'],
            [['charts', 2, 'points', 1, 'rgb', 2], 'Value must be no greater than 255.'],
        ];
        $detailedErrors = [];
        foreach ($detailedErrorsData as $errorData) {
            $detailedErrors[] = [$errorData[1], $errorData[0]];
        }

        $errorMessages = [
            'Value must be no less than -10.',
            'Custom error.',
            'Value must be no greater than 10.',
            'Value must be no less than 0.',
            'Value must be no greater than 255.',
            'Value must be no less than -10.',
            'Custom error.',
            'Value must be no greater than 10.',
            'Value must be no less than 0.',
            'Value must be no greater than 255.',
            'Custom error.',
            'Custom error.',
            'Value must be no less than -10.',
            'Custom error.',
            'Value must be no greater than 10.',
            'Value must be no less than 0.',
            'Value must be no greater than 255.',
            'Value must be no less than -10.',
            'Custom error.',
            'Value must be no greater than 10.',
            'Value must be no less than 0.',
            'Value must be no greater than 255.',
        ];
        $errorMessagesIndexedByPath = [
            'charts.0.points.0.coordinates.x' => ['Value must be no less than -10.', 'Custom error.'],
            'charts.0.points.0.coordinates.y' => ['Value must be no greater than 10.'],
            'charts.0.points.0.rgb.0' => ['Value must be no less than 0.'],
            'charts.0.points.0.rgb.1' => ['Value must be no greater than 255.'],
            'charts.0.points.1.coordinates.x' => ['Value must be no less than -10.', 'Custom error.'],
            'charts.0.points.1.coordinates.y' => ['Value must be no greater than 10.'],
            'charts.0.points.1.rgb.1' => ['Value must be no less than 0.'],
            'charts.0.points.1.rgb.2' => ['Value must be no greater than 255.'],
            'charts.1.points.0.coordinates.x' => ['Custom error.'],
            'charts.1.points.1.coordinates.x' => ['Custom error.'],
            'charts.2.points.0.coordinates.x' => ['Value must be no less than -10.', 'Custom error.'],
            'charts.2.points.0.coordinates.y' => ['Value must be no greater than 10.'],
            'charts.2.points.0.rgb.0' => ['Value must be no less than 0.'],
            'charts.2.points.0.rgb.1' => ['Value must be no greater than 255.'],
            'charts.2.points.1.coordinates.x' => ['Value must be no less than -10.', 'Custom error.'],
            'charts.2.points.1.coordinates.y' => ['Value must be no greater than 10.'],
            'charts.2.points.1.rgb.1' => ['Value must be no less than 0.'],
            'charts.2.points.1.rgb.2' => ['Value must be no greater than 255.'],
        ];

        return [
            'base' => [
                $data,
                [
                    new Nested([
                        'charts' => [
                            new Each([
                                new Nested([
                                    'points' => [
                                        new Each([
                                            new Nested([
                                                'coordinates' => new Nested([
                                                    'x' => $xRules,
                                                    'y' => $yRules,
                                                ]),
                                                'rgb' => $rgbRules,
                                            ]),
                                        ]),
                                    ],
                                ]),
                            ]),
                        ],
                    ]),
                ],
                $detailedErrors,
                $errorMessages,
                $errorMessagesIndexedByPath,
            ],
            // https://github.com/yiisoft/validator/issues/195
            'withShortcut' => [
                $data,
                [
                    new Nested([
                        'charts.*.points.*.coordinates.x' => $xRules,
                        'charts.*.points.*.coordinates.y' => $yRules,
                        'charts.*.points.*.rgb' => $rgbRules,
                    ]),
                ],
                $detailedErrors,
                $errorMessages,
                $errorMessagesIndexedByPath,
            ],
            'withShortcutAndWithoutShortcut' => [
                array_merge($data, ['active' => true]),
                [
                    new Nested([
                        'charts.*.points.*.coordinates.x' => $xRules,
                        'charts.*.points.*.coordinates.y' => $yRules,
                        'charts.*.points.*.rgb' => $rgbRules,
                        'active' => new BooleanValue(),
                    ]),
                ],
                $detailedErrors,
                $errorMessages,
                $errorMessagesIndexedByPath,
            ],
            'withShortcutAndGrouping' => [
                $data,
                [
                    new Nested([
                        'charts.*.points.*.coordinates' => new Nested([
                            'x' => $xRules,
                            'y' => $yRules,
                        ]),
                        'charts.*.points.*.rgb' => $rgbRules,
                    ]),
                ],
                $detailedErrors,
                $errorMessages,
                $errorMessagesIndexedByPath,
            ],
            'withShortcutAndKeysContainingSeparatorAndShortcut' => [
                [
                    'charts.list' => [
                        [
                            'points*list' => [
                                [
                                    'coordinates.data' => ['x' => -11, 'y' => 11],
                                    'rgb' => [-1, 256, 0],
                                ],
                            ],
                        ],
                    ],
                ],
                [
                    new Nested([
                        'charts\.list.*.points\*list.*.coordinates\.data.x' => $xRules,
                        'charts\.list.*.points\*list.*.coordinates\.data.y' => $yRules,
                        'charts\.list.*.points\*list.*.rgb' => $rgbRules,
                    ]),
                ],
                [
                    [
                        $errorMessages[0],
                        ['charts.list', 0, 'points*list', 0, 'coordinates.data', 'x'],
                    ],
                    [
                        $errorMessages[1],
                        ['charts.list', 0, 'points*list', 0, 'coordinates.data', 'x'],
                    ],
                    [
                        $errorMessages[2],
                        ['charts.list', 0, 'points*list', 0, 'coordinates.data', 'y'],
                    ],
                    [
                        $errorMessages[3],
                        ['charts.list', 0, 'points*list', 0, 'rgb', 0],
                    ],
                    [
                        $errorMessages[4],
                        ['charts.list', 0, 'points*list', 0, 'rgb', 1],
                    ],
                ],
                array_slice($errorMessages, 0, 5),
                [
                    'charts\.list.0.points\*list.0.coordinates\.data.x' => [$errorMessages[0], $errorMessages[1]],
                    'charts\.list.0.points\*list.0.coordinates\.data.y' => [$errorMessages[2]],
                    'charts\.list.0.points\*list.0.rgb.0' => [$errorMessages[3]],
                    'charts\.list.0.points\*list.0.rgb.1' => [$errorMessages[4]],
                ],
            ],
        ];
    }

    /**
     * @dataProvider dataWithOtherNestedAndEach
     */
    public function testWithOtherNestedAndEach(
        mixed $data,
        array $rules,
        array $expectedDetailedErrors,
        array $expectedErrorMessages,
        array $expectedErrorMessagesIndexedByPath
    ): void {
        $result = (new Validator())->validate($data, $rules);

        $errorsData = array_map(
            static fn (Error $error) => [
                $error->getMessage(),
                $error->getValuePath(),
            ],
            $result->getErrors()
        );

        $this->assertSame($expectedDetailedErrors, $errorsData);
        $this->assertSame($expectedErrorMessages, $result->getErrorMessages());
        $this->assertSame($expectedErrorMessagesIndexedByPath, $result->getErrorMessagesIndexedByPath());
    }

    public function dataValidationPassed(): array
    {
        return [
            [
                [
                    'author' => [
                        'name' => 'Dmitry',
                        'age' => 18,
                    ],
                ],
                [
                    new Nested([
                        'author.name' => [
                            new Length(min: 3),
                        ],
                    ]),
                ],
            ],
            [
                [
                    'author' => [
                        'name' => 'Dmitry',
                        'age' => 18,
                    ],
                ],
                [
                    new Nested([
                        'author' => [
                            new Required(),
                            new Nested([
                                'name' => [new Length(min: 3)],
                            ]),
                        ],
                    ]),
                ],
            ],
            'key not exists, skip empty' => [
                [
                    'author' => [
                        'name' => 'Dmitry',
                        'age' => 18,
                    ],
                ],
                [new Nested(['author.sex' => [new In(['male', 'female'], skipOnEmpty: true)]])],
            ],
            'keys containing separator, one nested rule' => [
                [
                    'author.data' => [
                        'name.surname' => 'Dmitriy',
                    ],
                ],
                [
                    new Nested([
                        'author\.data.name\.surname' => [
                            new Length(min: 3),
                        ],
                    ]),
                ],
            ],
            'keys containing separator, multiple nested rules' => [
                [
                    'author.data' => [
                        'name.surname' => 'Dmitriy',
                    ],
                ],
                [
                    new Nested([
                        'author\.data' => new Nested([
                            'name\.surname' => [
                                new Length(min: 3),
                            ],
                        ]),
                    ]),
                ],
            ],
            'property path of non-integer and non-string type, array' => [
                [0 => 'a', 1 => 'b'],
                [new Nested([false => new Length(min: 1), true => new Length(min: 1)])],
            ],
            'property path of non-integer and non-string type, iterator' => [
                [0 => 'a', 1 => 'b'],
                [new Nested(new IteratorWithBooleanKey())],
            ],
            'property path of non-integer and non-string type, generator' => [
                [0 => 'a', 1 => 'b'],
                [
                    new Nested(
                        new class () implements RulesProviderInterface {
                            public function getRules(): iterable
                            {
                                yield false => new Length(min: 1);
                                yield true => new Length(min: 1);
                            }
                        },
                    ),
                ],
            ],
            'iterator in rules' => [
                ['user' => ['age' => 19]],
                [new Nested(new ArrayObject(['user.age' => new Number(min: 18)]))],
            ],
        ];
    }

    public function dataValidationFailed(): array
    {
        $incorrectDataSet = new class () implements DataSetInterface {
            public function getAttributeValue(string $attribute): mixed
            {
                return false;
            }

            public function getData(): ?array
            {
                return null;
            }

            public function hasAttribute(string $attribute): bool
            {
                return false;
            }
        };

        return [
            // No rules with no object
            'no rules with no object, array' => [
                new class () {
                    #[Nested]
                    public array $value = [];
                },
                null,
                ['value' => ['Nested rule without rules can be used for objects only.']],
            ],
            'no rules with no object, boolean' => [
                new class () {
                    #[Nested]
                    public bool $value = false;
                },
                null,
                ['value' => ['Nested rule without rules can be used for objects only.']],
            ],
            'no rules with no object, integer' => [
                new class () {
                    #[Nested]
                    public int $value = 42;
                },
                null,
                ['value' => ['Nested rule without rules can be used for objects only.']],
            ],
            'custom no rules with no object message' => [
                new class () {
                    #[Nested(noRulesWithNoObjectMessage: 'Custom no rules with no object message.')]
                    public array $value = [];
                },
                null,
                ['value' => ['Custom no rules with no object message.']],
            ],
            'custom no rules with no object message with parameters' => [
                new class () {
                    #[Nested(noRulesWithNoObjectMessage: 'Attribute - {attribute}, type - {type}.')]
                    public array $value = [];
                },
                null,
                ['value' => ['Attribute - value, type - array.']],
            ],
            // Incorrect data set type
            'incorrect data set type' => [
                $incorrectDataSet,
                [new Nested(['value' => new Required()])],
                ['' => ['An object data set data can only have an array type.']],
            ],
            'custom incorrect data set type message' => [
                $incorrectDataSet,
                [
                    new Nested(
                        ['value' => new Required()],
                        incorrectDataSetTypeMessage: 'Custom incorrect data set type message.',
                    ),
                ],
                ['' => ['Custom incorrect data set type message.']],
            ],
            'custom incorrect data set type message with parameters' => [
                $incorrectDataSet,
                [new Nested(['value' => new Required()], incorrectDataSetTypeMessage: 'Type - {type}.')],
                ['' => ['Type - null.']],
            ],
            // Incorrect input
            'incorrect input' => [
                '',
                [new Nested(['value' => new Required()])],
                ['' => ['The value must be an array or an object.']],
            ],
            'custom incorrect input message' => [
                '',
                [new Nested(['value' => new Required()], incorrectInputMessage: 'Custom incorrect input message.')],
                ['' => ['Custom incorrect input message.']],
            ],
            'custom incorrect input message with parameters' => [
                '',
                [
                    new Nested(
                        ['value' => new Required()],
                        incorrectInputMessage: 'Attribute - {attribute}, type - {type}.',
                    ),
                ],
                ['' => ['Attribute - , type - string.']],
            ],
            'custom incorrect input message with parameters, attribute set' => [
                ['data' => ''],
                [
                    'data' => new Nested(
                        ['value' => new Required()],
                        incorrectInputMessage: 'Attribute - {attribute}, type - {type}.',
                    ),
                ],
                ['data' => ['Attribute - data, type - string.']],
            ],
            'error' => [
                [
                    'author' => [
                        'name' => 'Alex',
                        'age' => 38,
                    ],
                ],
                [new Nested(['author.age' => [new Number(min: 40)]])],
                ['author.age' => ['Value must be no less than 40.']],
            ],
            'key not exists' => [
                [
                    'author' => [
                        'name' => 'Alex',
                        'age' => 38,
                    ],
                ],
                [new Nested(['author.sex' => [new In(['male', 'female'])]])],
                ['author.sex' => ['This value is not in the list of acceptable values.']],
            ],
            [
                ['value' => null],
                [new Nested(['value' => new Required()])],
                ['value' => ['Value cannot be blank.']],
            ],
            [
                [],
                [new Nested(['value' => new Required()], requirePropertyPath: true)],
                ['value' => ['Property "value" is not found.']],
            ],
            [
                [],
                [new Nested([0 => new Required()], requirePropertyPath: true)],
                [0 => ['Property "0" is not found.']],
            ],
            // https://github.com/yiisoft/validator/issues/200
            [
                [
                    'body' => [
                        'shipping' => [
                            'phone' => '+777777777777',
                        ],
                    ],
                ],
                [
                    new Nested([
                        'body.shipping' => [
                            new Required(),
                            new Nested([
                                'phone' => [new Regex('/^\+\d{11}$/')],
                            ]),
                        ],
                    ]),
                ],
                ['body.shipping.phone' => ['Value is invalid.']],
            ],
            [
                [0 => [0 => -11]],
                [
                    new Nested([
                        0 => new Nested([
                            0 => [new Number(min: -10, max: 10)],
                        ]),
                    ]),
                ],
                ['0.0' => ['Value must be no less than -10.']],
            ],
            'custom error' => [
                [],
                [
                    new Nested(
                        ['value' => new Required()],
                        requirePropertyPath: true,
                        noPropertyPathMessage: 'Property is not found.',
                    ),
                ],
                ['value' => ['Property is not found.']],
            ],
            [
                new ObjectDataSet(
                    new class () {
                        private int $value = 7;
                    },
                    ReflectionProperty::IS_PUBLIC,
                ),
                new Nested(['value' => new Required()]),
                ['value' => ['Value cannot be blank.']],
            ],
            'nested context' => [
                [
                    'method' => 'get',
                    'attributes' => ['abc' => null],
                ],
                [
                    'method' => [new Required()],
                    'attributes' => new Nested([
                        'abc' => [
                            new Required(when: static function (mixed $value, ValidationContext $context): bool {
                                $method = $context->getGlobalDataSet()->getAttributeValue('method');
                                return $method === 'get';
                            }),
                        ],
                    ]),
                ],
                [
                    'attributes.abc' => ['Value cannot be blank.'],
                ],
            ],
            'deep level of nesting with plain keys' => [
                [
                    'level1' => [
                        'level2' => [
                            'level3' => [
                                'key' => 7,
                                'name' => 'var',
                            ],
                        ],
                    ],
                ],
                new Nested([
                    'level1' => [
                        'level2.level3' => [
                            'key' => new Integer(min: 9),
                        ],
                        'level2' => [
                            'level3.key' => [new Integer(max: 5)],
                        ],
                    ],
                    'level1.level2' => [
                        'level3.name' => new Length(min: 5),
                    ],
                ]),
                [
                    'level1.level2.level3.key' => ['Value must be no less than 9.', 'Value must be no greater than 5.'],
                    'level1.level2.level3.name' => ['This value must contain at least 5 characters.'],
                ],
            ],
        ];
    }

    public function dataValidationFailedWithDetailedErrors(): array
    {
        return [
            'error' => [
                [
                    'author' => [
                        'name' => 'Dmitry',
                        'age' => 18,
                    ],
                ],
                [new Nested(['author.age' => [new Number(min: 20)]])],
                [['Value must be no less than 20.', ['author', 'age']]],
            ],
            'key not exists' => [
                [
                    'author' => [
                        'name' => 'Dmitry',
                        'age' => 18,
                    ],
                ],
                [new Nested(['author.sex' => [new In(['male', 'female'])]])],
                [['This value is not in the list of acceptable values.', ['author', 'sex']]],
            ],
            [
                '',
                [new Nested(['value' => new Required()])],
                [['The value must be an array or an object.', []]],
            ],
            [
                ['value' => null],
                [new Nested(['value' => new Required()])],
                [['Value cannot be blank.', ['value']]],
            ],
            [
                [],
                [new Nested(['value1' => new Required(), 'value2' => new Required()], requirePropertyPath: true)],
                [
                    ['Property "value1" is not found.', ['value1']],
                    ['Property "value2" is not found.', ['value2']],
                ],
            ],
            [
                // https://github.com/yiisoft/validator/issues/200
                [
                    'body' => [
                        'shipping' => [
                            'phone' => '+777777777777',
                        ],
                    ],
                ],
                [
                    new Nested([
                        'body.shipping' => [
                            new Required(),
                            new Nested([
                                'phone' => [new Regex('/^\+\d{11}$/')],
                            ]),
                        ],
                    ]),
                ],
                [['Value is invalid.', ['body', 'shipping', 'phone']]],
            ],
            [
                [0 => [0 => -11]],
                [
                    new Nested([
                        0 => new Nested([
                            0 => [new Number(min: -10, max: 10)],
                        ]),
                    ]),
                ],
                [['Value must be no less than -10.', [0, 0]]],
            ],
            [
                [
                    'author.data' => [
                        'name.surname' => 'Dmitriy',
                    ],
                ],
                [new Nested(['author\.data.name\.surname' => [new Length(min: 8)]])],
                [['This value must contain at least 8 characters.', ['author.data', 'name.surname']]],
            ],
        ];
    }

    /**
     * @dataProvider dataValidationFailedWithDetailedErrors
     */
    public function testValidationFailedWithDetailedErrors(mixed $data, array $rules, array $errors): void
    {
        $result = (new Validator())->validate($data, $rules);

        $errorsData = array_map(
            static fn (Error $error) => [
                $error->getMessage(),
                $error->getValuePath(),
            ],
            $result->getErrors()
        );

        $this->assertFalse($result->isValid());
        $this->assertSame($errors, $errorsData);
    }

    public function testInitWithNotARule(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $message = 'Every rule must be an instance of Yiisoft\Validator\RuleInterface, string given.';
        $this->expectExceptionMessage($message);
        new Nested([
            'data' => new Nested([
                'title' => [new Length(max: 255)],
                'active' => [new BooleanValue(), 'Not a rule'],
            ]),
        ]);
    }

    public function testSkipOnError(): void
    {
        $this->testSkipOnErrorInternal(new Nested(), new Nested(skipOnError: true));
    }

    public function testWhen(): void
    {
        $when = static fn (mixed $value): bool => $value !== null;
        $this->testWhenInternal(new Nested(), new Nested(when: $when));
    }

    public function testInvalidRules(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'The $rules argument passed to Nested rule can be either: a null, an object implementing ' .
            'RulesProviderInterface, a class string or an iterable.'
        );
        new Nested(new Required());
    }

    protected function getDifferentRuleInHandlerItems(): array
    {
        return [Nested::class, NestedHandler::class];
    }
}
