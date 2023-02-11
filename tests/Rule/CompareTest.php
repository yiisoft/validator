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

use Yiisoft\Validator\Tests\Support\Data\CompareObject;

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
                        'template' => 'The allowed types are integer, float, string, boolean, null and object ' .
                            'implementing \Stringable interface.',
                        'parameters' => [
                            'targetValue' => 1,
                            'targetAttribute' => null,
                            'targetValueOrAttribute' => 1,
                        ],
                    ],
                    'incorrectDataSetTypeMessage' => [
                        'template' => 'The attribute value returned from a custom data set must have one of the ' .
                            'following types: integer, float, string, boolean, null or an object implementing ' .
                            '\Stringable interface.',
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
                new Compare(
                    new DateTime('2023-02-07 12:57:12'),
                    targetAttribute: 'test',
                    incorrectInputMessage: 'Custom message 1.',
                    incorrectDataSetTypeMessage: 'Custom message 2.',
                    message: 'Custom message 3.',
                    type: CompareType::ORIGINAL,
                    operator: '>=',
                    skipOnEmpty: true,
                    skipOnError: true,
                    when: static fn (): bool => true,
                ),
                [
                    'targetAttribute' => 'test',
                    'incorrectInputMessage' => [
                        'template' => 'Custom message 1.',
                        'parameters' => [
                            'targetAttribute' => 'test',
                        ],
                    ],
                    'incorrectDataSetTypeMessage' => [
                        'template' => 'Custom message 2.',
                        'parameters' => [
                            'targetAttribute' => 'test',
                        ],
                    ],
                    'message' => [
                        'template' => 'Custom message 3.',
                        'parameters' => [
                            'targetAttribute' => 'test',
                        ],
                    ],
                    'type' => 'original',
                    'operator' => '>=',
                    'skipOnEmpty' => true,
                    'skipOnError' => true,
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
        $object = new CompareObject(a: 1, b: 2);
        $objectWithDifferentPropertyType = new CompareObject(a: 1, b: '2');
        $array = [1, 2];

        return [
            // Number / string specific, expressions

            'target value: float, value: float with the same value as expression result, type: number, operator: ==' => [
                1 - 0.83,
                [new Compare(0.17)],
            ],
            'target value: float, value: float with the same value as expression result, type: number, operator: ===' => [
                1 - 0.83,
                [new Compare(0.17, operator: '===')],
            ],
            'target value: float, value: float with the same value as expression result, type: number, operator: >=' => [
                1 - 0.83,
                [new Compare(0.17, operator: '>=')],
            ],
            'target value: float, value: float with the same value as expression result, type: string, operator: ==' => [
                1 - 0.83,
                [new Compare(0.17, type: CompareType::STRING)],
            ],

            // Number / original specific, decimal places, directly provided values

            'target value: string float, value: string float with the same value, but extra decimal place (0), type: number, operator: ==' => [
                '100.50',
                [new Compare('100.5')],
            ],
            'target value: float, value: string float with the same value, but extra decimal place (0), type: number, operator: ==' => [
                '100.50',
                [new Compare(100.5)],
            ],
            'target value: string float, value: string float with the same value, but extra decimal place (0), type: number, operator: ===' => [
                '100.50',
                [new Compare('100.5', operator: '===')],
            ],
            'target value: string float, value: string float with the same value, but extra decimal place (0), type: original, operator: ==' => [
                '100.50',
                [new Compare('100.5', type: CompareType::ORIGINAL)],
            ],

            // Number / original specific, decimal places, values provided via stringable objects

            'target value: stringable float, value: stringable float with the same value, but extra decimal place (0), type: number, operator: ==' => [
                $stringableFloat,
                [new Compare($targetStringableFloat)],
            ],
            'target value: stringable float, value: stringable float with the same value, but extra decimal place (0), type: number, operator: >=' => [
                $stringableFloat,
                [new Compare($targetStringableFloat, operator: '>=')],
            ],

            // String / original specific, character order, directly provided values

            'target value: uuidv4, value: greater uuidv4, type: string, operator: >' => [
                'd62f2b3f-707f-451a-8819-046ff8436a4f',
                [new Compare('3b98a689-7d49-48bb-8741-7e27f220b69a', type: CompareType::STRING, operator: '>')],
            ],
            'target value: character, value: character located further within alphabet, type: string, operator: >' => [
                'b',
                [new Compare('a', type: CompareType::STRING, operator: '>')],
            ],
            'target value: character, value: character located further within alphabet, type: original, operator: >' => [
                'b',
                [new Compare('a', type: CompareType::ORIGINAL, operator: '>')],
            ],

            // String specific, character order, values provided via stringable objects

            'target value: stringable uuidv4, value: greater stringable uuidv4, type: string, operator: >' => [
                $stringableUuid,
                [new Compare($targetStringableUuid, type: CompareType::STRING, operator: '>')],
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

            // Original specific, objects

            'target value: object, value: similar object in a different instance, type: original, operator: ==' => [
                new stdClass(),
                [new Compare(new stdClass(), type: CompareType::ORIGINAL)],
            ],
            'target value: object, value: the same object, type: original, operator: ===' => [
                $object,
                [new Compare($object, type: CompareType::ORIGINAL, operator: '===')],
            ],
            'target value: object, value: similar object but with different property type, type: original, operator: ===' => [
                $objectWithDifferentPropertyType,
                [new Compare($object, type: CompareType::ORIGINAL)],
            ],

            // Original specific, arrays

            'target value: array, value: similar array declared separately, type: original, operator: ==' => [
                [1, 2],
                [new Compare([1, 2], type: CompareType::ORIGINAL)],
            ],
            'target value: array, value: similar array declared separately, type: original, operator: ===' => [
                [1, 2],
                [new Compare([1, 2], type: CompareType::ORIGINAL, operator: '===')],
            ],
            'target value: array, value: similar array but with different item type, type: original, operator: ==' => [
                [1, 2],
                [new Compare([1, '2'], type: CompareType::ORIGINAL)],
            ],
            'target value: array, value: the same array, type: original, operator: ===' => [
                $array,
                [new Compare($array, type: CompareType::ORIGINAL)],
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
        $subFloatFromInt = static function (int $value1, float $value2): int {
            return $value1 - (int) $value2;
        };
        $initialData = [
            // Basic

            'target value: integer, value: integer with the same value, type: number, operator: ==' => [
                100,
                [new Compare(100)],
            ],
            'target value: integer, value: integer with the same value, type: number, operator: ===' => [
                100,
                [new Compare(100, operator: '===')],
            ],
            'target value: integer, value: lower integer, type: number, operator: !=' => [
                99,
                [new Compare(100, operator: '!=')],
            ],
            'target value: integer, value: greater integer, type: number, operator: !=' => [
                101,
                [new Compare(100, operator: '!=')],
            ],
            'target value: integer, value: lower integer, type: number, operator: !==' => [
                101,
                [new Compare(100, operator: '!==')],
            ],
            'target value: integer, value: greater integer, type: number, operator: !==' => [
                101,
                [new Compare(100, operator: '!==')],
            ],
            'target value: integer, value: greater integer, type: number, operator: >' => [
                101,
                [new Compare(100, operator: '>')],
            ],
            'target value: integer, value: integer with the same value, type: number, operator: >=' => [
                100,
                [new Compare(100, operator: '>=')],
            ],
            'target value: integer, value: greater integer, type: number, operator: >=' => [
                101,
                [new Compare(100, operator: '>=')],
            ],
            'target value: integer, value: lower integer, type: number, operator: <' => [
                99,
                [new Compare(100, operator: '<')],
            ],
            'target value: integer, value: integer with the same value, type: number, operator: <=' => [
                100,
                [new Compare(100, operator: '<=')],
            ],
            'target value: integer, value: lower integer, type: number, operator: <=' => [
                99,
                [new Compare(100, operator: '<=')],
            ],

            // Boolean

            'target value: boolean (false), value: boolean (true), type: number, operator: >=' => [
                true,
                [new Compare(false, operator: '>=')],
            ],

            // Different types for non-strict equality

            'target value: empty string, value: null, type: number, operator: ==' => [
                null,
                [new Compare('')],
            ],
            'target value: integer, value: string integer with the same value, type: number, operator: ==' => [
                '100',
                [new Compare(100)],
            ],

            // Different types for non-strict inequality

            'target value: integer, value: float, type: number, operator: !=' => [
                100.00001,
                [new Compare(100, operator: '!=')],
            ],
            'target value: integer, value: boolean, type: number, operator: !=' => [
                false,
                [new Compare(100, operator: '!=')],
            ],

            // Different types for strict inequality

            'target value: integer, value: boolean, type: number, operator: !==' => [
                false,
                [new Compare(100, operator: '!==')],
            ],
            'target value: integer, value: string integer with the same value, type: number, operator: !==' => [
                '100',
                [new Compare(100, operator: '!==')],
            ],
            'target value: integer, value: float with the same value, but extra decimal place (0), type: number, operator: !==' => [
                100.0,
                [new Compare(100, operator: '!==')],
            ],

            // Large integers

            'target value: string with large integer, value: string with the same integer, type: number, operator: ===' => [
                PHP_INT_MAX . '0',
                [new Compare(PHP_INT_MAX . '0', operator: '===')],
            ],
            'target value: string with large integer, value: string with greater integer, type: number, operator: >' => [
                PHP_INT_MAX . '0',
                [new Compare('-' . PHP_INT_MAX . '12', operator: '>')],
            ],
            'target value: large integer in scientific notation, value: greater integer, type: number, operator: ===' => [
                4.5e19,
                [new Compare(4.5e19, operator: '===')],
            ],
            'target value: large integer in scientific notation, value: greater integer, type: number, operator: >' => [
                4.5e20,
                [new Compare(-4.5e19, operator: '>')],
            ],
            'target value: integer, value: the same integer as expression result, type: number, operator: ===' => [
                $subFloatFromInt(1234567890, 1234567890),
                [new Compare(0, operator: '===')],
            ],

            // Target attribute

            'target attribute: array key, target attribute value: integer, attribute value: integer with the same value, type: number, operator: ==' => [
                ['attribute' => 100, 'number' => 100],
                ['number' => new Compare(targetAttribute: 'attribute')],
            ],
            'target attribute: array key, target attribute value: integer, attribute value: lower integer, type: number, operator: <=' => [
                ['attribute' => 100, 'number' => 99],
                ['number' => new Compare(targetAttribute: 'attribute', operator: '<=')],
            ],
            'target attribute: object property, target attribute value: integer, attribute value: integer with the same value, type: number, operator: ==' => [
                new class () {
                    public int $attribute = 100;
                    public int $number = 100;
                },
                ['number' => new Compare(targetAttribute: 'attribute', operator: '<=')],
            ],
            'target attribute: custom data set attribute, target attribute value: integer, attribute value: integer with the same value, type: number, operator: ==' => [
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
        $object = new CompareObject(a: 1, b: 2);
        $objectWithDifferentPropertyValue = new CompareObject(a: 1, b: 3);
        $objectWithDifferentPropertyType = new CompareObject(a: 1, b: '2');
        $array = [1, 2];
        $reversedArray = [2, 1];

        return [
            // Incorrect input

            'incorrect input' => [
                [],
                [new Compare(false)],
                [
                    '' => [
                        'The allowed types are integer, float, string, boolean, null and object implementing ' .
                        '\Stringable interface.',
                    ],
                ],
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
                [
                    '' => [
                        'The attribute value returned from a custom data set must have one of the following types: ' .
                            'integer, float, string, boolean, null or an object implementing \Stringable interface.',
                    ],
                ],
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
                [new Compare(0, type: CompareType::STRING)],
                ['' => ['Value must be equal to "0".']],
            ],

            // Number / string specific, expressions

            'target value: float, value: float with the same value as expression result, type: original, operator: ==' => [
                1 - 0.83,
                [new Compare(0.17, type: CompareType::ORIGINAL)],
                ['' => ['Value must be equal to "0.17".']],
            ],

            // Number / original specific, decimal places, directly provided values

            'target value: string float, value: string float with the same value, but extra decimal place (0), type: string, operator: ==' => [
                '100.50', [new Compare('100.5', type: CompareType::STRING)], ['' => ['Value must be equal to "100.5".']],
            ],
            'target value: string float, value: string float with the same value, but extra decimal place (0), type: string, operator: ===' => [
                '100.50', [new Compare('100.5', type: CompareType::STRING, operator: '===')], ['' => ['Value must be strictly equal to "100.5".']],
            ],
            'target value: string float, value: string float with the same value, but extra decimal place (0), type: original, operator: ===' => [
                '100.50', [new Compare('100.5', type: CompareType::ORIGINAL, operator: '===')], ['' => ['Value must be strictly equal to "100.5".']],
            ],

            // Number / original specific, decimal places, values provided via stringable objects

            'target value: stringable float, value: stringable float with the same value, but extra decimal place (0), type: string, operator: ==' => [
                $stringableFloat,
                [new Compare($targetStringableFloat, type: CompareType::STRING)],
                ['' => ['Value must be equal to "100.5".']],
            ],
            'target value: stringable float, value: stringable float with the same value, but extra decimal place (0), type: string, operator: ===' => [
                $stringableFloat,
                [new Compare($targetStringableFloat, type: CompareType::STRING, operator: '===')],
                ['' => ['Value must be strictly equal to "100.5".']],
            ],
            'target value: stringable float, value: stringable float with the same value, but extra decimal place (0), type: original, operator: ==' => [
                $stringableFloat,
                [new Compare($targetStringableFloat, type: CompareType::ORIGINAL)],
                ['' => ['Value must be equal to "Stringable@anonymous".']],
            ],
            'target value: stringable float, value: stringable float with the same value, but extra decimal place (0), type: original, operator: ===' => [
                $stringableFloat,
                [new Compare($targetStringableFloat, type: CompareType::ORIGINAL, operator: '===')],
                ['' => ['Value must be strictly equal to "Stringable@anonymous".']],
            ],

            // String / original specific, character order, directly provided values

            'target value: character, value: character located further within alphabet, type: number, operator: >' => [
                'b',
                [new Compare('a', type: CompareType::NUMBER, operator: '>')],
                ['' => ['Value must be greater than "a".']],
            ],

            // String specific, character order, values provided via stringable objects

            'target value: stringable uuidv4, value: greater stringable uuidv4, type: number, operator: >' => [
                $stringableUuid,
                [new Compare($targetStringableUuid, type: CompareType::NUMBER, operator: '>')],
                ['' => ['Value must be greater than "3b98a689-7d49-48bb-8741-7e27f220b69a".']],
            ],
            'target value: stringable uuidv4, value: greater stringable uuidv4, type: original, operator: >' => [
                $stringableUuid,
                [new Compare($targetStringableUuid, type: CompareType::ORIGINAL, operator: '>')],
                ['' => ['Value must be greater than "Stringable@anonymous".']],
            ],

            // Original specific, datetime

            'target value: human-readable DateTime string, value: greater DateTime string, type: string, operator: >' => [
                '2022-06-03',
                [new Compare('June 2nd, 2022', type: CompareType::STRING, operator: '>')],
                ['' => ['Value must be greater than "June 2nd, 2022".']],
            ],

            // Original specific, objects

            'target value: object, value: similar object in a different instance, type: original, operator: ===' => [
                new stdClass(),
                [new Compare(new stdClass(), type: CompareType::ORIGINAL, operator: '===')],
                ['' => ['Value must be strictly equal to "stdClass".']],
            ],
            'target value: object, value: similar object with different property value, type: original, operator: ==' => [
                $objectWithDifferentPropertyValue,
                [new Compare($object, type: CompareType::ORIGINAL)],
                ['' => [sprintf('Value must be equal to "%s".', CompareObject::class)]],
            ],
            'target value: object, value: similar object with different property value, type: original, operator: ===' => [
                $objectWithDifferentPropertyValue,
                [new Compare($object, type: CompareType::ORIGINAL, operator: '===')],
                ['' => [sprintf('Value must be strictly equal to "%s".', CompareObject::class)]],
            ],
            'target value: object, value: similar object but with different property type, type: original, operator: ===' => [
                $objectWithDifferentPropertyType,
                [new Compare($object, type: CompareType::ORIGINAL, operator: '===')],
                ['' => [sprintf('Value must be strictly equal to "%s".', CompareObject::class)]],
            ],

            // Original specific, arrays

            'target value: array, value: similar array but with different item type, type: original, operator: ===' => [
                [1, 2],
                [new Compare([1, '2'], type: CompareType::ORIGINAL, operator: '===')],
                ['' => ['Value must be strictly equal to "array".']],
            ],
            'target value: array, value: similar array but with different items order, type: original, operator: ==' => [
                $reversedArray,
                [new Compare($array, type: CompareType::ORIGINAL)],
                ['' => ['Value must be equal to "array".']],
            ],
            'target value: array, value: similar array but reversed, type: original, operator: ===' => [
                $reversedArray,
                [new Compare($array, type: CompareType::ORIGINAL, operator: '===')],
                ['' => ['Value must be strictly equal to "array".']],
            ],
        ];
    }

    public function dataValidationFailedWithDifferentTypes(): array
    {
        $messageEqual = 'Value must be equal to "100".';
        $messageStrictlyEqual = 'Value must be strictly equal to "100".';
        $messageNotEqual = 'Value must not be equal to "100".';
        $messageNotStrictlyEqual = 'Value must not be strictly equal to "100".';
        $messageGreaterThan = 'Value must be greater than "100".';
        $messageGreaterOrEqualThan = 'Value must be greater than or equal to "100".';
        $messageLessThan = 'Value must be less than "100".';
        $messageLessOrEqualThan = 'Value must be less than or equal to "100".';
        $initialData = [
            // Basic

            'target value: integer, value: lower integer, type: number, operator: ==' => [
                99,
                [new Compare(100)],
                ['' => [$messageEqual]],
            ],
            'target value: integer, value: greater integer, type: number, operator: ==' => [
                101,
                [new Compare(100)],
                ['' => [$messageEqual]],
            ],
            'target value: integer, value: lower integer, type: number, operator: ===' => [
                99,
                [new Compare(100, operator: '===')],
                ['' => [$messageStrictlyEqual]],
            ],
            'target value: integer, value: greater integer, type: number, operator: ===' => [
                101,
                [new Compare(100, operator: '===')],
                ['' => [$messageStrictlyEqual]],
            ],
            'target value: integer, value: integer with the same value, type: number, operator: !=' => [
                100,
                [new Compare(100, operator: '!=')],
                ['' => [$messageNotEqual]],
            ],
            'target value: integer, value: integer with the same value, type: number, operator: !==' => [
                100,
                [new Compare(100, operator: '!==')],
                ['' => [$messageNotStrictlyEqual]],
            ],
            'target value: integer, value: integer with the same value, type: number, operator: >' => [
                100,
                [new Compare(100, operator: '>')],
                ['' => [$messageGreaterThan]],
            ],
            'target value: integer, value: lower integer, type: number, operator: >' => [
                99,
                [new Compare(100, operator: '>')],
                ['' => [$messageGreaterThan]],
            ],
            'target value: integer, value: lower integer, type: number, operator: >=' => [
                99,
                [new Compare(100, operator: '>=')],
                ['' => [$messageGreaterOrEqualThan]],
            ],
            'target value: integer, value: integer with the same value, type: number, operator: <' => [
                100,
                [new Compare(100, operator: '<')],
                ['' => [$messageLessThan]],
            ],
            'target value: integer, value: greater integer, type: number, operator: <' => [
                101,
                [new Compare(100, operator: '<')],
                ['' => [$messageLessThan]],
            ],
            'target value: integer, value: greater integer, type: number, operator: <=' => [
                101,
                [new Compare(100, operator: '<=')],
                ['' => [$messageLessOrEqualThan]],
            ],

            // Different types for strict equality

            'target value: empty string, value: null, type: number, operator: ===' => [
                null,
                [new Compare('', operator: '===')],
                ['' => ['Value must be strictly equal to "".']],
            ],
            'target value: integer, value: string integer with the same value, type: number, operator: ===' => [
                '100',
                [new Compare(100, operator: '===')],
                ['' => [$messageStrictlyEqual]],
            ],
            'target value: integer, value: float with the same value, but extra decimal place (0), type: number, operator: ===' => [
                100.0,
                [new Compare(100, operator: '===')],
                ['' => [$messageStrictlyEqual]],
            ],

            // Different types for non-strict inequality

            'target value: integer, value: string integer with the same value, type: number, operator: !=' => [
                '100',
                [new Compare(100, operator: '!=')],
                ['' => [$messageNotEqual]],
            ],
            'target value: integer, value: float with the same value, but extra decimal place (0), type: number, operator: !=' => [
                100.0,
                [new Compare(100, operator: '!=')],
                ['' => [$messageNotEqual]],
            ],

            // Target attribute

            'target attribute: array key, target attribute value: string integer, attribute value: integer with the same value, type: number, operator: ===' => [
                ['attribute' => '100', 'number' => 100],
                ['number' => new Compare(targetAttribute: 'attribute', operator: '===')],
                ['number' => ['Value must be strictly equal to "attribute".']],
            ],
            'target attribute: array key, target attribute value: integer, attribute value: greater integer, type: number, operator: <=' => [
                ['attribute' => 100, 'number' => 101],
                ['number' => new Compare(targetAttribute: 'attribute', operator: '<=')],
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
        $mainType = CompareType::NUMBER;
        $remainingTypes = [CompareType::ORIGINAL, CompareType::STRING];
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
