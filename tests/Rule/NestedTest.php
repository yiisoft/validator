<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;
use stdClass;
use Yiisoft\Arrays\ArrayHelper;
use Yiisoft\Validator\Error;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\Callback;
use Yiisoft\Validator\Rule\Count;
use Yiisoft\Validator\Rule\Each;
use Yiisoft\Validator\Rule\HasLength;
use Yiisoft\Validator\Rule\InRange;
use Yiisoft\Validator\Rule\Nested;
use Yiisoft\Validator\Rule\NestedHandler;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Rule\Regex;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\RulesProviderInterface;
use Yiisoft\Validator\Tests\Support\Data\EachNestedObjects\Foo;
use Yiisoft\Validator\Tests\Support\ValidatorFactory;
use Yiisoft\Validator\Tests\Support\Data\InheritAttributesObject\InheritAttributesObject;
use Yiisoft\Validator\Tests\Support\Data\ObjectWithDifferentPropertyVisibility;
use Yiisoft\Validator\Tests\Support\Data\ObjectWithNestedObject;
use Yiisoft\Validator\Tests\Support\Rule\StubRule\StubRule;
use Yiisoft\Validator\Tests\Support\RulesProvider\SimpleRulesProvider;
use Yiisoft\Validator\Tests\Support\Rule\RuleWithCustomHandler;
use Yiisoft\Validator\ValidationContext;
use Yiisoft\Validator\Validator;

final class NestedTest extends TestCase
{
    public function testDefaultValues(): void
    {
        $rule = new Nested();

        $this->assertNull($rule->getRules());
        $this->assertSame(
            ReflectionProperty::IS_PRIVATE | ReflectionProperty::IS_PROTECTED | ReflectionProperty::IS_PUBLIC,
            $rule->getPropertyVisibility(),
        );
        $this->assertFalse($rule->getRequirePropertyPath());
        $this->assertSame('Property path "{path}" is not found.', $rule->getNoPropertyPathMessage());
        $this->assertNull($rule->getSkipOnEmpty());
        $this->assertFalse($rule->shouldSkipOnError());
        $this->assertNull($rule->getWhen());
    }

    public function testPropertyVisibilityInConstructor(): void
    {
        $rule = new Nested(propertyVisibility: ReflectionProperty::IS_PRIVATE);

        $this->assertSame(ReflectionProperty::IS_PRIVATE, $rule->getPropertyVisibility());
    }

    public function testSkipOnEmptyInConstructor(): void
    {
        $rule = new Nested(skipOnEmpty: true);

        $this->assertTrue($rule->getSkipOnEmpty());
    }

    public function testSkipOnEmptySetter(): void
    {
        $rule = (new Nested())->skipOnEmpty(true);

        $this->assertTrue($rule->getSkipOnEmpty());
    }

    public function testGetName(): void
    {
        $rule = new Nested();

        $this->assertSame('nested', $rule->getName());
    }

    public function testHandlerClassName(): void
    {
        $rule = new Nested();

        $this->assertSame(NestedHandler::class, $rule->getHandlerClassName());
    }

    public function dataOptions(): array
    {
        return [
            [
                new Nested([new Number(integerPattern: '/1/', numberPattern: '/1/')]),
                [
                    'requirePropertyPath' => false,
                    'noPropertyPathMessage' => [
                        'message' => 'Property path "{path}" is not found.',
                    ],
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                    'rules' => [
                        [
                            'number',
                            'asInteger' => false,
                            'min' => null,
                            'max' => null,
                            'notANumberMessage' => [
                                'message' => 'Value must be a number.',
                            ],
                            'tooSmallMessage' => [
                                'message' => 'Value must be no less than {min}.',
                                'parameters' => ['min' => null],
                            ],
                            'tooBigMessage' => [
                                'message' => 'Value must be no greater than {max}.',
                                'parameters' => ['max' => null],
                            ],
                            'skipOnEmpty' => false,
                            'skipOnError' => false,
                            'integerPattern' => '/1/',
                            'numberPattern' => '/1/',
                        ],
                    ],
                ],
            ],
            [
                new Nested(['user.age' => new Number(integerPattern: '/1/', numberPattern: '/1/')]),
                [
                    'requirePropertyPath' => false,
                    'noPropertyPathMessage' => [
                        'message' => 'Property path "{path}" is not found.',
                    ],
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                    'rules' => [
                        'user.age' => [
                            'number',
                            'asInteger' => false,
                            'min' => null,
                            'max' => null,
                            'notANumberMessage' => [
                                'message' => 'Value must be a number.',
                            ],
                            'tooSmallMessage' => [
                                'message' => 'Value must be no less than {min}.',
                                'parameters' => ['min' => null],
                            ],
                            'tooBigMessage' => [
                                'message' => 'Value must be no greater than {max}.',
                                'parameters' => ['max' => null],
                            ],
                            'skipOnEmpty' => false,
                            'skipOnError' => false,
                            'integerPattern' => '/1/',
                            'numberPattern' => '/1/',
                        ],
                    ],
                ],
            ],
            [
                new Nested([
                    'author.name' => new StubRule('author-name', ['key' => 'name']),
                    'author.age' => new StubRule('author-age', ['key' => 'age']),
                ]),
                [
                    'requirePropertyPath' => false,
                    'noPropertyPathMessage' => [
                        'message' => 'Property path "{path}" is not found.',
                    ],
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                    'rules' => [
                        'author.name' => ['author-name', 'key' => 'name'],
                        'author.age' => ['author-age', 'key' => 'age'],
                    ],
                ],
            ],
            [
                new Nested([
                    'author' => [
                        'name' => new StubRule('author-name', ['key' => 'name']),
                        'age' => new StubRule('author-age', ['key' => 'age']),
                    ],
                ]),
                [
                    'requirePropertyPath' => false,
                    'noPropertyPathMessage' => [
                        'message' => 'Property path "{path}" is not found.',
                    ],
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                    'rules' => [
                        'author' => [
                            'name' => ['author-name', 'key' => 'name'],
                            'age' => ['author-age', 'key' => 'age'],
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @dataProvider dataOptions
     */
    public function testOptions(Nested $rule, array $expectedOptions): void
    {
        $options = $rule->getOptions();

        $this->assertEquals($expectedOptions, $options);
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

    public function dataNestedWithoutRulesToNonObject(): array
    {
        return [
            'array' => [
                'array',
                new class () {
                    #[Nested]
                    public array $value = [];
                },
            ],
            'boolean' => [
                'bool',
                new class () {
                    #[Nested]
                    public bool $value = false;
                },
            ],
            'integer' => [
                'int',
                new class () {
                    #[Nested]
                    public int $value = 42;
                },
            ],
        ];
    }

    /**
     * @dataProvider dataNestedWithoutRulesToNonObject
     */
    public function testNestedWithoutRulesToNonObject(string $expectedValueName, object $data): void
    {
        $validator = ValidatorFactory::make();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Nested rule without rules could be used for objects only. ' . $expectedValueName . ' given.'
        );
        $validator->validate($data);
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
                        rulesPropertyVisibility: ReflectionProperty::IS_PRIVATE
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
            'wo-rules' => [
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
            'wo-rules-only-public' => [
                new class () {
                    #[Nested(propertyVisibility: ReflectionProperty::IS_PUBLIC)]
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
            'wo-rules-only-protected' => [
                new class () {
                    #[Nested(propertyVisibility: ReflectionProperty::IS_PROTECTED)]
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
            'wo-rules-inherit-attributes' => [
                new class () {
                    #[Nested]
                    private $object;

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
            'nested-into-each' => [
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
    public function testHandler(
        object $data,
        array $expectedErrorMessagesIndexedByPath
    ): void {
        $result = ValidatorFactory::make()->validate($data);
        $this->assertSame($expectedErrorMessagesIndexedByPath, $result->getErrorMessagesIndexedByPath());
    }

    public function testPropagateOptions(): void
    {
        $rule = new Nested([
            'posts' => [
                new Each([
                    new Nested([
                        'title' => [new HasLength(min: 3)],
                        'authors' => [
                            new Each([
                                new Nested([
                                    'name' => [new HasLength(min: 5)],
                                    'age' => [
                                        new Number(min: 18),
                                        new Number(min: 20),
                                    ],
                                ]),
                            ]),
                        ],
                    ]),
                ]),
            ],
            'meta' => [new HasLength(min: 7)],
        ], propagateOptions: true, skipOnEmpty: true, skipOnError: true);
        $options = $rule->getOptions();
        $paths = [
            [],
            ['rules', 'posts', 0],
            ['rules', 'posts', 0, 'rules', 0],
            ['rules', 'posts', 0, 'rules', 0, 'rules', 'title', 0],
            ['rules', 'posts', 0, 'rules', 0, 'rules', 'authors', 0],
            ['rules', 'posts', 0, 'rules', 0, 'rules', 'authors', 0, 'rules', 0],
            ['rules', 'posts', 0, 'rules', 0, 'rules', 'authors', 0, 'rules', 0, 'rules', 'name', 0],
            ['rules', 'posts', 0, 'rules', 0, 'rules', 'authors', 0, 'rules', 0, 'rules', 'age', 0],
            ['rules', 'posts', 0, 'rules', 0, 'rules', 'authors', 0, 'rules', 0, 'rules', 'age', 1],
            ['rules', 'meta', 0],
        ];
        $keys = ['skipOnEmpty', 'skipOnError'];

        foreach ($paths as $path) {
            foreach ($keys as $key) {
                $fullPath = $path;
                $fullPath[] = $key;

                $value = ArrayHelper::getValueByPath($options, $fullPath);
                $this->assertTrue($value);
            }
        }
    }

    public function testNestedWithoutRulesWithObject(): void
    {
        $validator = ValidatorFactory::make();
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
            new Count(exactly: 3),
            new Each([new Number(min: 0, max: 255)]),
        ];

        $detailedErrorsData = [
            [['charts', 0, 'points', 0, 'coordinates', 'x'], 'Value must be no less than -10.'],
            [['charts', 0, 'points', 0, 'coordinates', 'x'], 'Custom error.'],
            [['charts', 0, 'points', 0, 'coordinates', 'y'], 'Value must be no greater than 10.'],
            [['charts', 0, 'points', 0, 'rgb', 0], 'Value must be no less than 0. -1 given.'],
            [['charts', 0, 'points', 0, 'rgb', 1], 'Value must be no greater than 255. 256 given.'],
            [['charts', 0, 'points', 1, 'coordinates', 'x'], 'Value must be no less than -10.'],
            [['charts', 0, 'points', 1, 'coordinates', 'x'], 'Custom error.'],
            [['charts', 0, 'points', 1, 'coordinates', 'y'], 'Value must be no greater than 10.'],
            [['charts', 0, 'points', 1, 'rgb', 1], 'Value must be no less than 0. -2 given.'],
            [['charts', 0, 'points', 1, 'rgb', 2], 'Value must be no greater than 255. 257 given.'],
            [['charts', 1, 'points', 0, 'coordinates', 'x'], 'Custom error.'],
            [['charts', 1, 'points', 1, 'coordinates', 'x'], 'Custom error.'],
            [['charts', 2, 'points', 0, 'coordinates', 'x'], 'Value must be no less than -10.'],
            [['charts', 2, 'points', 0, 'coordinates', 'x'], 'Custom error.'],
            [['charts', 2, 'points', 0, 'coordinates', 'y'], 'Value must be no greater than 10.'],
            [['charts', 2, 'points', 0, 'rgb', 0], 'Value must be no less than 0. -3 given.'],
            [['charts', 2, 'points', 0, 'rgb', 1], 'Value must be no greater than 255. 258 given.'],
            [['charts', 2, 'points', 1, 'coordinates', 'x'], 'Value must be no less than -10.'],
            [['charts', 2, 'points', 1, 'coordinates', 'x'], 'Custom error.'],
            [['charts', 2, 'points', 1, 'coordinates', 'y'], 'Value must be no greater than 10.'],
            [['charts', 2, 'points', 1, 'rgb', 1], 'Value must be no less than 0. -4 given.'],
            [['charts', 2, 'points', 1, 'rgb', 2], 'Value must be no greater than 255. 259 given.'],
        ];
        $detailedErrors = [];
        foreach ($detailedErrorsData as $errorData) {
            $detailedErrors[] = [$errorData[1], $errorData[0]];
        }

        $errorMessages = [
            'Value must be no less than -10.',
            'Custom error.',
            'Value must be no greater than 10.',
            'Value must be no less than 0. -1 given.',
            'Value must be no greater than 255. 256 given.',
            'Value must be no less than -10.',
            'Custom error.',
            'Value must be no greater than 10.',
            'Value must be no less than 0. -2 given.',
            'Value must be no greater than 255. 257 given.',
            'Custom error.',
            'Custom error.',
            'Value must be no less than -10.',
            'Custom error.',
            'Value must be no greater than 10.',
            'Value must be no less than 0. -3 given.',
            'Value must be no greater than 255. 258 given.',
            'Value must be no less than -10.',
            'Custom error.',
            'Value must be no greater than 10.',
            'Value must be no less than 0. -4 given.',
            'Value must be no greater than 255. 259 given.',
        ];
        $errorMessagesIndexedByPath = [
            'charts.0.points.0.coordinates.x' => ['Value must be no less than -10.', 'Custom error.'],
            'charts.0.points.0.coordinates.y' => ['Value must be no greater than 10.'],
            'charts.0.points.0.rgb.0' => ['Value must be no less than 0. -1 given.'],
            'charts.0.points.0.rgb.1' => ['Value must be no greater than 255. 256 given.'],
            'charts.0.points.1.coordinates.x' => ['Value must be no less than -10.', 'Custom error.'],
            'charts.0.points.1.coordinates.y' => ['Value must be no greater than 10.'],
            'charts.0.points.1.rgb.1' => ['Value must be no less than 0. -2 given.'],
            'charts.0.points.1.rgb.2' => ['Value must be no greater than 255. 257 given.'],
            'charts.1.points.0.coordinates.x' => ['Custom error.'],
            'charts.1.points.1.coordinates.x' => ['Custom error.'],
            'charts.2.points.0.coordinates.x' => ['Value must be no less than -10.', 'Custom error.'],
            'charts.2.points.0.coordinates.y' => ['Value must be no greater than 10.'],
            'charts.2.points.0.rgb.0' => ['Value must be no less than 0. -3 given.'],
            'charts.2.points.0.rgb.1' => ['Value must be no greater than 255. 258 given.'],
            'charts.2.points.1.coordinates.x' => ['Value must be no less than -10.', 'Custom error.'],
            'charts.2.points.1.coordinates.y' => ['Value must be no greater than 10.'],
            'charts.2.points.1.rgb.1' => ['Value must be no less than 0. -4 given.'],
            'charts.2.points.1.rgb.2' => ['Value must be no greater than 255. 259 given.'],
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
        $result = ValidatorFactory::make()->validate($data, $rules);

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
                            new HasLength(min: 3),
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
                                'name' => [new HasLength(min: 3)],
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
                [new Nested(['author.sex' => [new InRange(['male', 'female'], skipOnEmpty: true)]])],
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
                            new HasLength(min: 3),
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
                                new HasLength(min: 3),
                            ],
                        ]),
                    ]),
                ],
            ],
        ];
    }

    /**
     * @dataProvider dataValidationPassed
     */
    public function testValidationPassed(mixed $data, array $rules): void
    {
        $result = ValidatorFactory::make()->validate($data, $rules);

        $this->assertTrue($result->isValid());
    }

    public function dataValidationFailed(): array
    {
        return [
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
                [new Nested(['author.sex' => [new InRange(['male', 'female'])]])],
                ['author.sex' => ['This value is invalid.']],
            ],
            [
                '',
                [new Nested(['value' => new Required()])],
                ['' => ['Value should be an array or an object. string given.']],
            ],
            [
                ['value' => null],
                [new Nested(['value' => new Required()])],
                ['value' => ['Value cannot be blank.']],
            ],
            [
                [],
                [new Nested(['value' => new Required()], requirePropertyPath: true)],
                ['value' => ['Property path "value" is not found.']],
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
        ];
    }

    /**
     * @dataProvider dataValidationFailed
     */
    public function testValidationFailed(mixed $data, array $rules, array $errorMessagesIndexedByPath): void
    {
        $result = ValidatorFactory::make()->validate($data, $rules);

        $this->assertFalse($result->isValid());
        $this->assertSame($errorMessagesIndexedByPath, $result->getErrorMessagesIndexedByPath());
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
                [new Nested(['author.sex' => [new InRange(['male', 'female'])]])],
                [['This value is invalid.', ['author', 'sex']]],
            ],
            [
                '',
                [new Nested(['value' => new Required()])],
                [['Value should be an array or an object. string given.', []]],
            ],
            [
                ['value' => null],
                [new Nested(['value' => new Required()])],
                [['Value cannot be blank.', ['value']]],
            ],
            [
                [],
                [new Nested(['value' => new Required()], requirePropertyPath: true)],
                [['Property path "value" is not found.', ['value']]],
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
                [new Nested(['author\.data.name\.surname' => [new HasLength(min: 8)]])],
                [['This value must contain at least 8 characters.', ['author.data', 'name.surname']]],
            ],
        ];
    }

    /**
     * @dataProvider dataValidationFailedWithDetailedErrors
     */
    public function testValidationFailedWithDetailedErrors(mixed $data, array $rules, array $errors): void
    {
        $result = ValidatorFactory::make()->validate($data, $rules);

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

    public function testCustomErrorMessage(): void
    {
        $data = [];
        $rules = [
            new Nested(
                ['value' => new Required()],
                requirePropertyPath: true,
                noPropertyPathMessage: 'Property is not found.',
            ),
        ];

        $result = ValidatorFactory::make()->validate($data, $rules);

        $this->assertFalse($result->isValid());
        $this->assertSame(
            ['value' => ['Property is not found.']],
            $result->getErrorMessagesIndexedByPath()
        );
    }

    public function testDifferentRuleInHandler(): void
    {
        $rule = new RuleWithCustomHandler(NestedHandler::class);
        $validator = ValidatorFactory::make();

        $this->expectExceptionMessageMatches(
            '/.*' . preg_quote(Nested::class) . '.*' . preg_quote(RuleWithCustomHandler::class) . '.*/'
        );
        $validator->validate([], [$rule]);
    }
}
