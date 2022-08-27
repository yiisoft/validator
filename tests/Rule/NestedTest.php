<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;
use stdClass;
use Yiisoft\Validator\Rule\Nested;
use Yiisoft\Validator\Rule\NestedHandler;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\RulesProviderInterface;
use Yiisoft\Validator\SimpleRuleHandlerContainer;
use Yiisoft\Validator\SkipOnEmptyCallback\SkipNone;
use Yiisoft\Validator\SkipOnEmptyCallback\SkipOnEmpty;
use Yiisoft\Validator\SkipOnEmptyCallback\SkipOnNull;
use Yiisoft\Validator\Tests\Stub\InheritAttributesObject\InheritAttributesObject;
use Yiisoft\Validator\Tests\Stub\ObjectWithDifferentPropertyVisibility;
use Yiisoft\Validator\Tests\Stub\Rule;
use Yiisoft\Validator\Tests\Stub\SimpleRulesProvider;
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
        $this->assertFalse($rule->getSkipOnEmpty());
        $this->assertInstanceOf(SkipNone::class, $rule->getSkipOnEmptyCallback());
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

    public function testSkipOnEmptyCallbackInConstructor(): void
    {
        $rule = new Nested(skipOnEmptyCallback: new SkipOnNull());

        $this->assertInstanceOf(SkipOnNull::class, $rule->getSkipOnEmptyCallback());
    }

    public function testSkipOnEmptySetter(): void
    {
        $rule = (new Nested())->skipOnEmpty(true);

        $this->assertTrue($rule->getSkipOnEmpty());
    }

    public function testSkipOnEmptyCallbackSetter(): void
    {
        $rule = (new Nested())->skipOnEmptyCallback(new SkipOnEmpty());

        $this->assertInstanceOf(SkipOnEmpty::class, $rule->getSkipOnEmptyCallback());
    }

    public function testGetName(): void
    {
        $rule = new Nested();

        $this->assertEquals('nested', $rule->getName());
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
                    'author.name' => new Rule('author-name', ['key' => 'name']),
                    'author.age' => new Rule('author-age', ['key' => 'age']),
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
                        'name' => new Rule('author-name', ['key' => 'name']),
                        'age' => new Rule('author-age', ['key' => 'age']),
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

    public function testValidationEmptyRules(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Nested([]);
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
        $validator = $this->createValidator();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Nested rule without rules available for objects only. ' . $expectedValueName . ' given.'
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
        ];
    }

    /**
     * @dataProvider dataHandler
     */
    public function testHandler(
        object $data,
        array $expectedErrorMessagesIndexedByPath,
        ?bool $expectedIsValid = false
    ): void {
        $result = $this->createValidator()->validate($data);

        $this->assertSame($expectedIsValid, $result->isValid());
        if (!$expectedIsValid) {
            $this->assertSame($expectedErrorMessagesIndexedByPath, $result->getErrorMessagesIndexedByPath());
        }
    }

    private function createValidator(): Validator
    {
        return new Validator(new SimpleRuleHandlerContainer());
    }
}
