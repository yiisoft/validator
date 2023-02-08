<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use DateTime;
use InvalidArgumentException;
use RuntimeException;
use stdClass;
use Stringable;
use Yiisoft\Validator\DataSetInterface;
use Yiisoft\Validator\DataWrapperInterface;
use Yiisoft\Validator\Rule\Compare;
use Yiisoft\Validator\Rule\CompareType;
use Yiisoft\Validator\RuleInterface;
use Yiisoft\Validator\Tests\Rule\Base\RuleTestCase;
use Yiisoft\Validator\Tests\Rule\Base\RuleWithOptionsTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\SkipOnErrorTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\WhenTestTrait;

use function is_string;

final class CompareTest extends RuleTestCase
{
    use RuleWithOptionsTestTrait;
    use SkipOnErrorTestTrait;
    use WhenTestTrait;

    public function testInitWithWrongType(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $message = 'Type "float" is not supported. The valid types are: "original", "string", "number".';
        $this->expectExceptionMessage($message);

        new Compare(type: 'float');
    }

    public function testInitWithWrongOperator(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $message = 'Operator "=" is not supported. The valid operators are: "==", "===", "!=", "!==", ">", ">=", ' .
            '"<", "<=".';
        $this->expectExceptionMessage($message);

        new Compare(1, operator: '=');
    }

    public function testGetName(): void
    {
        $rule = new Compare();
        $this->assertSame('compare', $rule->getName());
    }

    public function dataOptions(): array
    {
        return [
            [
                new Compare(1),
                [
                    'targetValue' => 1,
                    'targetAttribute' => null,
                    'incorrectInputMessage' => [
                        'template' => 'The allowed types are integer, float, string, boolean and null.',
                        'parameters' => [
                            'targetValue' => 1,
                            'targetAttribute' => null,
                            'targetValueOrAttribute' => 1,
                        ],
                    ],
                    'incorrectDataSetTypeMessage' => [
                        'template' => 'The attribute value returned from a custom data set must have a scalar type or be null.',
                        'parameters' => [
                            'targetValue' => 1,
                            'targetAttribute' => null,
                            'targetValueOrAttribute' => 1,
                        ],
                    ],
                    'message' => [
                        'template' => 'Value must be equal to "{targetValueOrAttribute}".',
                        'parameters' => [
                            'targetValue' => 1,
                            'targetAttribute' => null,
                            'targetValueOrAttribute' => 1,
                        ],
                    ],
                    'type' => 'string',
                    'operator' => '==',
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                new Compare(1, type: CompareType::NUMBER),
                [
                    'targetValue' => 1,
                    'targetAttribute' => null,
                    'incorrectInputMessage' => [
                        'template' => 'The allowed types are integer, float, string, boolean and null.',
                        'parameters' => [
                            'targetValue' => 1,
                            'targetAttribute' => null,
                            'targetValueOrAttribute' => 1,
                        ],
                    ],
                    'incorrectDataSetTypeMessage' => [
                        'template' => 'The attribute value returned from a custom data set must have a scalar type or be null.',
                        'parameters' => [
                            'targetValue' => 1,
                            'targetAttribute' => null,
                            'targetValueOrAttribute' => 1,
                        ],
                    ],
                    'message' => [
                        'template' => 'Value must be equal to "{targetValueOrAttribute}".',
                        'parameters' => [
                            'targetValue' => 1,
                            'targetAttribute' => null,
                            'targetValueOrAttribute' => 1,
                        ],
                    ],
                    'type' => 'number',
                    'operator' => '==',
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                new Compare(1, type: CompareType::NUMBER, operator: '>='),
                [
                    'targetValue' => 1,
                    'targetAttribute' => null,
                    'incorrectInputMessage' => [
                        'template' => 'The allowed types are integer, float, string, boolean and null.',
                        'parameters' => [
                            'targetValue' => 1,
                            'targetAttribute' => null,
                            'targetValueOrAttribute' => 1,
                        ],
                    ],
                    'incorrectDataSetTypeMessage' => [
                        'template' => 'The attribute value returned from a custom data set must have a scalar type or be null.',
                        'parameters' => [
                            'targetValue' => 1,
                            'targetAttribute' => null,
                            'targetValueOrAttribute' => 1,
                        ],
                    ],
                    'message' => [
                        'template' => 'Value must be greater than or equal to "{targetValueOrAttribute}".',
                        'parameters' => [
                            'targetValue' => 1,
                            'targetAttribute' => null,
                            'targetValueOrAttribute' => 1,
                        ],
                    ],
                    'type' => 'number',
                    'operator' => '>=',
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                new Compare('YES'),
                [
                    'targetValue' => 'YES',
                    'targetAttribute' => null,
                    'incorrectInputMessage' => [
                        'template' => 'The allowed types are integer, float, string, boolean and null.',
                        'parameters' => [
                            'targetValue' => 'YES',
                            'targetAttribute' => null,
                            'targetValueOrAttribute' => 'YES',
                        ],
                    ],
                    'incorrectDataSetTypeMessage' => [
                        'template' => 'The attribute value returned from a custom data set must have a scalar type or be null.',
                        'parameters' => [
                            'targetValue' => 'YES',
                            'targetAttribute' => null,
                            'targetValueOrAttribute' => 'YES',
                        ],
                    ],
                    'message' => [
                        'template' => 'Value must be equal to "{targetValueOrAttribute}".',
                        'parameters' => [
                            'targetValue' => 'YES',
                            'targetAttribute' => null,
                            'targetValueOrAttribute' => 'YES',
                        ],
                    ],
                    'type' => 'string',
                    'operator' => '==',
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                new Compare('YES', skipOnEmpty: true),
                [
                    'targetValue' => 'YES',
                    'targetAttribute' => null,
                    'incorrectInputMessage' => [
                        'template' => 'The allowed types are integer, float, string, boolean and null.',
                        'parameters' => [
                            'targetValue' => 'YES',
                            'targetAttribute' => null,
                            'targetValueOrAttribute' => 'YES',
                        ],
                    ],
                    'incorrectDataSetTypeMessage' => [
                        'template' => 'The attribute value returned from a custom data set must have a scalar type or be null.',
                        'parameters' => [
                            'targetValue' => 'YES',
                            'targetAttribute' => null,
                            'targetValueOrAttribute' => 'YES',
                        ],
                    ],
                    'message' => [
                        'template' => 'Value must be equal to "{targetValueOrAttribute}".',
                        'parameters' => [
                            'targetValue' => 'YES',
                            'targetAttribute' => null,
                            'targetValueOrAttribute' => 'YES',
                        ],
                    ],
                    'type' => 'string',
                    'operator' => '==',
                    'skipOnEmpty' => true,
                    'skipOnError' => false,
                ],
            ],
            [
                new Compare('YES', operator: '!=='),
                [
                    'targetValue' => 'YES',
                    'targetAttribute' => null,
                    'incorrectInputMessage' => [
                        'template' => 'The allowed types are integer, float, string, boolean and null.',
                        'parameters' => [
                            'targetValue' => 'YES',
                            'targetAttribute' => null,
                            'targetValueOrAttribute' => 'YES',
                        ],
                    ],
                    'incorrectDataSetTypeMessage' => [
                        'template' => 'The attribute value returned from a custom data set must have a scalar type or be null.',
                        'parameters' => [
                            'targetValue' => 'YES',
                            'targetAttribute' => null,
                            'targetValueOrAttribute' => 'YES',
                        ],
                    ],
                    'message' => [
                        'template' => 'Value must not be equal to "{targetValueOrAttribute}".',
                        'parameters' => [
                            'targetValue' => 'YES',
                            'targetAttribute' => null,
                            'targetValueOrAttribute' => 'YES',
                        ],
                    ],
                    'type' => 'string',
                    'operator' => '!==',
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                new Compare('YES', message: 'Custom message for {targetValueOrAttribute}.'),
                [
                    'targetValue' => 'YES',
                    'targetAttribute' => null,
                    'incorrectInputMessage' => [
                        'template' => 'The allowed types are integer, float, string, boolean and null.',
                        'parameters' => [
                            'targetValue' => 'YES',
                            'targetAttribute' => null,
                            'targetValueOrAttribute' => 'YES',
                        ],
                    ],
                    'incorrectDataSetTypeMessage' => [
                        'template' => 'The attribute value returned from a custom data set must have a scalar type or be null.',
                        'parameters' => [
                            'targetValue' => 'YES',
                            'targetAttribute' => null,
                            'targetValueOrAttribute' => 'YES',
                        ],
                    ],
                    'message' => [
                        'template' => 'Custom message for {targetValueOrAttribute}.',
                        'parameters' => [
                            'targetValue' => 'YES',
                            'targetAttribute' => null,
                            'targetValueOrAttribute' => 'YES',
                        ],
                    ],
                    'type' => 'string',
                    'operator' => '==',
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                new Compare(null, 'test'),
                [
                    'targetValue' => null,
                    'targetAttribute' => 'test',
                    'incorrectInputMessage' => [
                        'template' => 'The allowed types are integer, float, string, boolean and null.',
                        'parameters' => [
                            'targetValue' => null,
                            'targetAttribute' => 'test',
                            'targetValueOrAttribute' => 'test',
                        ],
                    ],
                    'incorrectDataSetTypeMessage' => [
                        'template' => 'The attribute value returned from a custom data set must have a scalar type or be null.',
                        'parameters' => [
                            'targetValue' => null,
                            'targetAttribute' => 'test',
                            'targetValueOrAttribute' => 'test',
                        ],
                    ],
                    'message' => [
                        'template' => 'Value must be equal to "{targetValueOrAttribute}".',
                        'parameters' => [
                            'targetValue' => null,
                            'targetAttribute' => 'test',
                            'targetValueOrAttribute' => 'test',
                        ],
                    ],
                    'type' => 'string',
                    'operator' => '==',
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                new Compare(
                    null,
                    'test',
                    incorrectInputMessage: 'Custom message 1.',
                    incorrectDataSetTypeMessage: 'Custom message 2.',
                    message: 'Custom message 3.',
                ),
                [
                    'targetValue' => null,
                    'targetAttribute' => 'test',
                    'incorrectInputMessage' => [
                        'template' => 'Custom message 1.',
                        'parameters' => [
                            'targetValue' => null,
                            'targetAttribute' => 'test',
                            'targetValueOrAttribute' => 'test',
                        ],
                    ],
                    'incorrectDataSetTypeMessage' => [
                        'template' => 'Custom message 2.',
                        'parameters' => [
                            'targetValue' => null,
                            'targetAttribute' => 'test',
                            'targetValueOrAttribute' => 'test',
                        ],
                    ],
                    'message' => [
                        'template' => 'Custom message 3.',
                        'parameters' => [
                            'targetValue' => null,
                            'targetAttribute' => 'test',
                            'targetValueOrAttribute' => 'test',
                        ],
                    ],
                    'type' => 'string',
                    'operator' => '==',
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                new Compare(1, 'test'),
                [
                    'targetValue' => 1,
                    'targetAttribute' => 'test',
                    'incorrectInputMessage' => [
                        'template' => 'The allowed types are integer, float, string, boolean and null.',
                        'parameters' => [
                            'targetValue' => 1,
                            'targetAttribute' => 'test',
                            'targetValueOrAttribute' => 1,
                        ],
                    ],
                    'incorrectDataSetTypeMessage' => [
                        'template' => 'The attribute value returned from a custom data set must have a scalar type or be null.',
                        'parameters' => [
                            'targetValue' => 1,
                            'targetAttribute' => 'test',
                            'targetValueOrAttribute' => 1,
                        ],
                    ],
                    'message' => [
                        'template' => 'Value must be equal to "{targetValueOrAttribute}".',
                        'parameters' => [
                            'targetValue' => 1,
                            'targetAttribute' => 'test',
                            'targetValueOrAttribute' => 1,
                        ],
                    ],
                    'type' => 'string',
                    'operator' => '==',
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
        ];
    }

    public function dataValidationPassed(): array
    {
        $targetStringableFloat = new class () implements Stringable {
            public function __toString(): string
            {
                return '100.5';
            }
        };
        $stringableFloat = new class () implements Stringable {
            public function __toString(): string
            {
                return '100.50';
            }
        };
        $targetStringableUuid = new class () implements Stringable {
            public function __toString(): string
            {
                return '3b98a689-7d49-48bb-8741-7e27f220b69a';
            }
        };
        $stringableUuid = new class () implements Stringable {
            public function __toString(): string
            {
                return 'd62f2b3f-707f-451a-8819-046ff8436a4f';
            }
        };
        $dateTime = new DateTime('2023-02-07 12:57:12');

        return [
            // Number specific, expressions

            'target value: float, value: float with the same value as expression result, type: number, operator: ==' => [
                1 - 0.83,
                [new Compare(0.17, type: CompareType::NUMBER)],
            ],
            'target value: float, value: float with the same value as expression result, type: number, operator: ===' => [
                1 - 0.83,
                [new Compare(0.17, type: CompareType::NUMBER, operator: '===')],
            ],
            'target value: float, value: float with the same value as expression result, type: number, operator: >=' => [
                1 - 0.83,
                [new Compare(0.17, type: CompareType::NUMBER, operator: '>=')],
            ],

            // Number / original specific, decimal places, directly provided values

            'target value: string float, value: string float with the same value, but extra decimal place (0), type: number, operator: ==' => [
                '100.50',
                [new Compare('100.5', type: CompareType::NUMBER)],
            ],
            'target value: float, value: string float with the same value, but extra decimal place (0), type: number, operator: ==' => [
                '100.50',
                [new Compare(100.5, type: CompareType::NUMBER)],
            ],
            'target value: string float, value: string float with the same value, but extra decimal place (0), type: number, operator: ===' => [
                '100.50',
                [new Compare('100.5', type: CompareType::NUMBER, operator: '===')],
            ],
            'target value: string float, value: string float with the same value, but extra decimal place (0), type: original, operator: ==' => [
                '100.50', [new Compare('100.5', type: CompareType::ORIGINAL)], ['' => ['Value must be equal to "100.5".']],
            ],

            // Number / original specific, decimal places, values provided via stringable objects

            'target value: stringable float, value: stringable float with the same value, but extra decimal place (0), type: number, operator: ==' => [
                $stringableFloat,
                [new Compare($targetStringableFloat, type: CompareType::NUMBER)],
            ],
            'target value: stringable float, value: stringable float with the same value, but extra decimal place (0), type: number, operator: >=' => [
                $stringableFloat,
                [new Compare($targetStringableFloat, type: CompareType::NUMBER, operator: '>=')],
            ],

            // String / original specific, character order, directly provided values

            'target value: uuidv4, value: greater uuidv4, type: string, operator: >' => [
                'd62f2b3f-707f-451a-8819-046ff8436a4f',
                [new Compare('3b98a689-7d49-48bb-8741-7e27f220b69a', operator: '>')],
            ],
            'target value: character, value: character located further within alphabet, type: string, operator: ==' => [
                'b',
                [new Compare('a', operator: '>')],
            ],

            // String / original specific, character order, values provided via stringable objects

            'target value: stringable uuidv4, value: greater stringable uuidv4, type: string, operator: >' => [
                $stringableUuid,
                [new Compare($targetStringableUuid, operator: '>')],
            ],

            // Original specific, datetime

            'target value: DateTime object, value: DateTime object with the same value, type: original, operator: ==' => [
                new DateTime('2023-02-07 12:57:12'),
                [new Compare(new DateTime('2023-02-07 12:57:12'), type: CompareType::ORIGINAL)],
            ],
            'target value: DateTime object, value: the same DateTime object, type: original, operator: ===' => [
                $dateTime,
                [new Compare($dateTime, type: CompareType::ORIGINAL)],
            ],
            'target value: DateTime object, value: DateTime object with the same value, type: original, operator: !==' => [
                new DateTime('2023-02-07 12:57:12'),
                [new Compare(new DateTime('2023-02-07 12:57:12'), type: CompareType::ORIGINAL, operator: '!==')],
            ],
            'target value: DateTime object, value: DateTime object with the same value, type: original, operator: >=' => [
                new DateTime('2023-02-07 12:57:12'),
                [new Compare(new DateTime('2023-02-07 12:57:12'), type: CompareType::ORIGINAL, operator: '>=')],
            ],
            'target value: human-readable DateTime object, value: greater DateTime object, type: original, operator: >' => [
                new DateTime('2022-06-03'),
                [new Compare(new DateTime('June 2nd, 2022'), type: CompareType::ORIGINAL, operator: '>')],
            ],
        ];
    }

    public function dataValidationPassedWithDifferentTypes(): array
    {
        $customDataSet = new class () implements DataSetInterface {
            public function getAttributeValue(string $attribute): mixed
            {
                return 100;
            }

            public function getData(): ?array
            {
                return null;
            }

            public function hasAttribute(string $attribute): bool
            {
                return true;
            }
        };
        $initialData = [
            // Basic

            'target value: integer, value: integer with the same value, type: string, operator: ==' => [
                100,
                [new Compare(100)],
            ],
            'target value: integer, value: integer with the same value, type: string, operator: ===' => [
                100,
                [new Compare(100, operator: '===')],
            ],
            'target value: integer, value: lower integer, type: string, operator: !=' => [
                99,
                [new Compare(100, operator: '!=')],
            ],
            'target value: integer, value: greater integer, type: string, operator: !=' => [
                101,
                [new Compare(100, operator: '!=')],
            ],
            'target value: integer, value: lower integer, type: string, operator: !==' => [
                101,
                [new Compare(100, operator: '!==')],
            ],
            'target value: integer, value: greater integer, type: string, operator: !==' => [
                101,
                [new Compare(100, operator: '!==')],
            ],
            'target value: integer, value: greater integer, type: string, operator: >' => [
                101,
                [new Compare(100, operator: '>')],
            ],
            'target value: integer, value: integer with the same value, type: string, operator: >=' => [
                100,
                [new Compare(100, operator: '>=')],
            ],
            'target value: integer, value: greater integer, type: string, operator: >=' => [
                101,
                [new Compare(100, operator: '>=')],
            ],
            'target value: integer, value: lower integer, type: string, operator: <' => [
                99,
                [new Compare(100, operator: '<')],
            ],
            'target value: integer, value: integer with the same value, type: string, operator: <=' => [
                100,
                [new Compare(100, operator: '<=')],
            ],
            'target value: integer, value: lower integer, type: string, operator: <=' => [
                99,
                [new Compare(100, operator: '<=')],
            ],

            // Boolean

            'target value: boolean (false), value: boolean (true), type: string, operator: >=' => [
                true,
                [new Compare(false, operator: '>=')],
            ],

            // Different types for non-strict equality

            'target value: empty string, value: null, type: string, operator: ==' => [
                null,
                [new Compare('')],
            ],
            'target value: integer, value: string integer with the same value, type: string, operator: ==' => [
                '100',
                [new Compare(100)],
            ],

            // Different types for non-strict inequality

            'target value: integer, value: float, type: string, operator: !=' => [
                100.00001,
                [new Compare(100, operator: '!=')],
            ],
            'target value: integer, value: boolean, type: string, operator: !=' => [
                false,
                [new Compare(100, operator: '!=')],
            ],

            // Different types for strict inequality

            'target value: integer, value: boolean, type: string, operator: !==' => [
                false,
                [new Compare(100, operator: '!==')],
            ],
            'target value: integer, value: string integer with the same value, type: string, operator: !==' => [
                '100',
                [new Compare(100, operator: '!==')],
            ],
            'target value: integer, value: float with the same value, but extra decimal place (0), type: string, operator: !==' => [
                100.0,
                [new Compare(100, operator: '!==')],
            ],

            // Target attribute

            'target attribute: array key, target attribute value: integer, attribute value: integer with the same value, type: string, operator: ==' => [
                ['attribute' => 100, 'number' => 100],
                ['number' => new Compare(targetAttribute: 'attribute')],
            ],
            'target attribute: array key, target attribute value: integer, attribute value: lower integer, type: string, operator: <=' => [
                ['attribute' => 100, 'number' => 99],
                ['number' => new Compare(targetAttribute: 'attribute', operator: '<=')],
            ],
            'target attribute: object property, target attribute value: integer, attribute value: integer with the same value, type: string, operator: ==' => [
                new class () {
                    public int $attribute = 100;
                    public int $number = 100;
                },
                ['number' => new Compare(targetAttribute: 'attribute', operator: '<=')],
            ],
            'target attribute: custom data set attribute, target attribute value: integer, attribute value: integer with the same value, type: string, operator: ==' => [
                $customDataSet,
                ['number' => new Compare(targetAttribute: 'attribute', operator: '<=')],
            ],
        ];

        return $this->extendDataWithDifferentTypes($initialData);
    }

    /**
     * @dataProvider dataValidationPassed
     * @dataProvider dataValidationPassedWithDifferentTypes
     */
    public function testValidationPassed(mixed $data, ?array $rules = null): void
    {
        parent::testValidationPassed($data, $rules);
    }

    public function dataValidationFailed(): array
    {
        $incorrectDataSet = new class () implements DataWrapperInterface {
            public function getAttributeValue(string $attribute): mixed
            {
                return new stdClass();
            }

            public function getData(): ?array
            {
                return null;
            }

            public function getSource(): mixed
            {
                return false;
            }

            public function hasAttribute(string $attribute): bool
            {
                return false;
            }
        };
        $targetStringableFloat = new class () implements Stringable {
            public function __toString(): string
            {
                return '100.5';
            }
        };
        $stringableFloat = new class () implements Stringable {
            public function __toString(): string
            {
                return '100.50';
            }
        };

        return [
            // Incorrect input

            'incorrect input' => [
                [],
                [new Compare(false)],
                ['' => ['The allowed types are integer, float, string, boolean and null.']],
            ],
            'custom incorrect input message' => [
                [],
                [new Compare(false, incorrectInputMessage: 'Custom incorrect input message.')],
                ['' => ['Custom incorrect input message.']],
            ],
            'custom incorrect input message with parameters' => [
                [],
                [new Compare(false, incorrectInputMessage: 'Attribute - {attribute}, type - {type}.')],
                ['' => ['Attribute - , type - array.']],
            ],
            'custom incorrect input message with parameters, attribute set' => [
                ['data' => []],
                ['data' => new Compare(false, incorrectInputMessage: 'Attribute - {attribute}, type - {type}.')],
                ['data' => ['Attribute - data, type - array.']],
            ],

            // Incorrect data set input

            'incorrect data set type' => [
                $incorrectDataSet,
                [new Compare(targetAttribute: 'test')],
                ['' => ['The attribute value returned from a custom data set must have a scalar type or be null.']],
            ],
            'custom incorrect data set type message' => [
                $incorrectDataSet,
                [
                    new Compare(
                        targetAttribute: 'test',
                        incorrectDataSetTypeMessage: 'Custom incorrect data set type message.',
                    ),
                ],
                ['' => ['Custom incorrect data set type message.']],
            ],
            'custom incorrect data set type message with parameters' => [
                $incorrectDataSet,
                [
                    new Compare(
                        targetAttribute: 'test',
                        incorrectDataSetTypeMessage: 'Type - {type}.',
                    ),
                ],
                ['' => ['Type - stdClass.']],
            ],

            // Custom message

            'custom message' => [101, [new Compare(100, message: 'Custom message.')], ['' => ['Custom message.']]],
            'custom message with parameters, target value set' => [
                101,
                [
                    new Compare(
                        100,
                        message: 'Attribute - {attribute}, target value - {targetValue}, target attribute - ' .
                        '{targetAttribute}, target value or attribute - {targetValueOrAttribute}, value - {value}.',
                    ),
                ],
                [
                    '' => [
                        'Attribute - , target value - 100, target attribute - , target value or attribute - 100, ' .
                        'value - 101.',
                    ],
                ],
            ],
            'custom message with parameters, attribute and target attribute set' => [
                ['attribute' => 100, 'number' => 101],
                [
                    'number' => new Compare(
                        null,
                        'attribute',
                        message: 'Attribute - {attribute}, target value - {targetValue}, target attribute - ' .
                        '{targetAttribute}, target attribute value - {targetAttributeValue}, target value or ' .
                        'attribute - {targetValueOrAttribute}, value - {value}.',
                        operator: '===',
                    ),
                ],
                [
                    'number' => [
                        'Attribute - number, target value - , target attribute - attribute, target attribute value ' .
                        '- 100, target value or attribute - attribute, value - 101.',
                    ],
                ],
            ],

            // String / original specific, falsy values

            'target value: integer (0), value: null, type: string, operator: ==' => [
                null,
                [new Compare(0)],
                ['' => ['Value must be equal to "0".']],
            ],

            // Number / original specific, decimal places, directly provided values

            'target value: string float, value: string float with the same value, but extra decimal place (0), type: string, operator: ==' => [
                '100.50', [new Compare('100.5')], ['' => ['Value must be equal to "100.5".']],
            ],
            'target value: string float, value: string float with the same value, but extra decimal place (0), type: string, operator: ===' => [
                '100.50', [new Compare('100.5', operator: '===')], ['' => ['Value must be equal to "100.5".']],
            ],
            'target value: string float, value: string float with the same value, but extra decimal place (0), type: original, operator: ===' => [
                '100.50', [new Compare('100.5', type: CompareType::ORIGINAL, operator: '===')], ['' => ['Value must be equal to "100.5".']],
            ],

            // Number / original specific, decimal places, values provided via stringable objects

            'target value: stringable float, value: stringable float with the same value, but extra decimal place (0), type: string, operator: ==' => [
                $stringableFloat,
                [new Compare($targetStringableFloat)],
                ['' => ['Value must be equal to "100.5".']],
            ],
            'target value: stringable float, value: stringable float with the same value, but extra decimal place (0), type: string, operator: ===' => [
                $stringableFloat,
                [new Compare($targetStringableFloat, operator: '===')],
                ['' => ['Value must be equal to "100.5".']],
            ],
            'target value: stringable float, value: stringable float with the same value, but extra decimal place (0), type: original, operator: ==' => [
                $stringableFloat,
                [new Compare($targetStringableFloat, type: CompareType::ORIGINAL)],
                ['' => ['Value must be equal to "100.5".']],
            ],
            'target value: stringable float, value: stringable float with the same value, but extra decimal place (0), type: original, operator: ===' => [
                $stringableFloat,
                [new Compare($targetStringableFloat, type: CompareType::ORIGINAL, operator: '===')],
                ['' => ['Value must be equal to "100.5".']],
            ],

            // Original specific, datetime

            'target value: human-readable DateTime string, value: greater DateTime string, type: string, operator: >' => [
                '2022-06-03',
                [new Compare('June 2nd, 2022', operator: '>')],
                ['' => ['Value must be greater than "June 2nd, 2022".']],
            ],
        ];
    }

    public function dataValidationFailedWithDifferentTypes(): array
    {
        $messageEqual = 'Value must be equal to "100".';
        $messageNotEqual = 'Value must not be equal to "100".';
        $messageGreaterThan = 'Value must be greater than "100".';
        $messageGreaterOrEqualThan = 'Value must be greater than or equal to "100".';
        $messageLessThan = 'Value must be less than "100".';
        $messageLessOrEqualThan = 'Value must be less than or equal to "100".';
        $initialData = [
            // Basic

            'target value: integer, value: lower integer, type: string, operator: ==' => [
                99,
                [new Compare(100)],
                ['' => [$messageEqual]],
            ],
            'target value: integer, value: greater integer, type: string, operator: ==' => [
                101,
                [new Compare(100)],
                ['' => [$messageEqual]],
            ],
            'target value: integer, value: lower integer, type: string, operator: ===' => [
                99,
                [new Compare(100, operator: '===')],
                ['' => [$messageEqual]],
            ],
            'target value: integer, value: greater integer, type: string, operator: ===' => [
                101,
                [new Compare(100, operator: '===')],
                ['' => [$messageEqual]],
            ],
            'target value: integer, value: integer with the same value, type: string, operator: !=' => [
                100,
                [new Compare(100, operator: '!=')],
                ['' => [$messageNotEqual]],
            ],
            'target value: integer, value: integer with the same value, type: string, operator: !==' => [
                100,
                [new Compare(100, operator: '!==')],
                ['' => [$messageNotEqual]],
            ],
            'target value: integer, value: integer with the same value, type: string, operator: >' => [
                100,
                [new Compare(100, operator: '>')],
                ['' => [$messageGreaterThan]],
            ],
            'target value: integer, value: lower integer, type: string, operator: >' => [
                99,
                [new Compare(100, operator: '>')],
                ['' => [$messageGreaterThan]],
            ],
            'target value: integer, value: lower integer, type: string, operator: >=' => [
                99,
                [new Compare(100, operator: '>=')],
                ['' => [$messageGreaterOrEqualThan]],
            ],
            'target value: integer, value: integer with the same value, type: string, operator: <' => [
                100,
                [new Compare(100, operator: '<')],
                ['' => [$messageLessThan]],
            ],
            'target value: integer, value: greater integer, type: string, operator: <' => [
                101,
                [new Compare(100, operator: '<')],
                ['' => [$messageLessThan]],
            ],
            'target value: integer, value: greater integer, type: string, operator: <=' => [
                101,
                [new Compare(100, operator: '<=')],
                ['' => [$messageLessOrEqualThan]],
            ],

            // Different types for strict equality

            'target value: empty string, value: null, type: string, operator: ===' => [
                null,
                [new Compare('', operator: '===')],
                ['' => ['Value must be equal to "".']],
            ],
            'target value: integer, value: string integer with the same value, type: string, operator: ===' => [
                '100',
                [new Compare(100, operator: '===')],
                ['' => [$messageEqual]],
            ],
            'target value: integer, value: float with the same value, but extra decimal place (0), type: string, operator: ===' => [
                100.0,
                [new Compare(100, operator: '===')],
                ['' => [$messageEqual]],
            ],

            // Different types for non-strict inequality

            'target value: integer, value: string integer with the same value, type: string, operator: !=' => [
                '100',
                [new Compare(100, operator: '!=')],
                ['' => [$messageNotEqual]],
            ],
            'target value: integer, value: float with the same value, but extra decimal place (0), type: string, operator: !=' => [
                100.0,
                [new Compare(100, operator: '!=')],
                ['' => [$messageNotEqual]],
            ],

            // Target attribute

            'target attribute: array key, target attribute value: string integer, attribute value: integer with the same value, type: string, operator: ===' => [
                ['attribute' => '100', 'number' => 100],
                ['number' => new Compare(null, 'attribute', operator: '===')],
                ['number' => ['Value must be equal to "attribute".']],
            ],
            'target attribute: array key, target attribute value: integer, attribute value: greater integer, type: string, operator: <=' => [
                ['attribute' => 100, 'number' => 101],
                ['number' => new Compare(null, 'attribute', operator: '<=')],
                ['number' => ['Value must be less than or equal to "attribute".']],
            ],
        ];

        return $this->extendDataWithDifferentTypes($initialData);
    }

    /**
     * @dataProvider dataValidationFailed
     * @dataProvider dataValidationFailedWithDifferentTypes
     */
    public function testValidationFailed(
        mixed $data,
        array|RuleInterface|null $rules,
        array $errorMessagesIndexedByPath,
    ): void {
        parent::testValidationFailed($data, $rules, $errorMessagesIndexedByPath);
    }

    private function extendDataWithDifferentTypes(array $initialData): array
    {
        $dynamicData = [];
        $mainType = CompareType::STRING;
        $remainingTypes = [CompareType::ORIGINAL, CompareType::NUMBER];
        foreach ($remainingTypes as $type) {
            foreach ($initialData as $key => $item) {
                $rules = [];
                foreach ($item[1] as $attribute => $rule) {
                    if (!$rule instanceof Compare) {
                        throw new RuntimeException('Wrong format for rule.');
                    }

                    $rules[$attribute] = new Compare(
                        targetValue: $rule->getTargetValue(),
                        targetAttribute: $rule->getTargetAttribute(),
                        type: $type,
                        operator: $rule->getOperator(),
                    );
                }

                if (!is_string($key)) {
                    throw new RuntimeException('Data set must have a string name.');
                }

                $newKey = str_replace(", type: $mainType,", ", type: $type,", $key);
                if ($key === $newKey) {
                    throw new RuntimeException('Wrong format for type.');
                }

                $itemData = [$item[0], $rules];
                if (isset($item[2])) {
                    $itemData[] = $item[2];
                }

                $dynamicData[$newKey] = $itemData;
            }
        }

        return array_merge($initialData, $dynamicData);
    }

    public function testSkipOnError(): void
    {
        $this->testSkipOnErrorInternal(new Compare(), new Compare(skipOnError: true));
    }

    public function testWhen(): void
    {
        $when = static fn (mixed $value): bool => $value !== null;
        $this->testWhenInternal(new Compare(), new Compare(when: $when));
    }
}
