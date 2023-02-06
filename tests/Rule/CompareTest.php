<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use InvalidArgumentException;
use stdClass;
use Yiisoft\Validator\DataWrapperInterface;
use Yiisoft\Validator\Rule\Compare;
use Yiisoft\Validator\Rule\CompareType;
use Yiisoft\Validator\Tests\Rule\Base\RuleTestCase;
use Yiisoft\Validator\Tests\Rule\Base\RuleWithOptionsTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\SkipOnErrorTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\WhenTestTrait;

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
                        'template' => 'The attribute value returned from a custom data set must have a scalar type.',
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
                        'template' => 'The attribute value returned from a custom data set must have a scalar type.',
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
                        'template' => 'The attribute value returned from a custom data set must have a scalar type.',
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
                        'template' => 'The attribute value returned from a custom data set must have a scalar type.',
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
                        'template' => 'The attribute value returned from a custom data set must have a scalar type.',
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
                        'template' => 'The attribute value returned from a custom data set must have a scalar type.',
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
                        'template' => 'The attribute value returned from a custom data set must have a scalar type.',
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
                        'template' => 'The attribute value returned from a custom data set must have a scalar type.',
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
                        'template' => 'The attribute value returned from a custom data set must have a scalar type.',
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
        return [
            [null, [new Compare('')]],

            [100, [new Compare(100)]],
            [['attribute' => 100, 'number' => 100], ['number' => new Compare(null, 'attribute')]],
            ['100', [new Compare(100)]],

            [100, [new Compare(100, operator: '===')]],

            [100.00001, [new Compare(100, operator: '!=')]],
            [false, [new Compare(100, operator: '!=')]],

            [101, [new Compare(100, operator: '>')]],

            [100, [new Compare(100, operator: '>=')]],
            [101, [new Compare(100, operator: '>=')]],
            [99, [new Compare(100, operator: '<')]],

            [100, [new Compare(100, operator: '<=')]],
            [99, [new Compare(100, operator: '<=')]],
            [['attribute' => 100, 'number' => 99], ['number' => new Compare(null, 'attribute', operator: '<=')]],

            ['100.50', [new Compare('100.5', type: CompareType::NUMBER)]],
            ['100.50', [new Compare(100.5, type: CompareType::NUMBER)]],
            ['100.50', [new Compare('100.5', type: CompareType::NUMBER, operator: '===')]],

            'integer !== boolean' => [false, [new Compare(100, operator: '!==')]],
            'integer !== string' => ['100', [new Compare(100, operator: '!==')]],
            'integer !== float' => [100.0, [new Compare(100, operator: '!==')]],

            'float == the same float as expression result' => [
                1 - 0.83,
                [new Compare(0.17, type: CompareType::NUMBER)],
            ],
            'float === the same float as expression result' => [
                1 - 0.83,
                [new Compare(0.17, type: CompareType::NUMBER, operator: '===')],
            ],
        ];
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
        $messageEqual = 'Value must be equal to "100".';
        $messageNotEqual = 'Value must not be equal to "100".';
        $messageGreaterThan = 'Value must be greater than "100".';
        $messageGreaterOrEqualThan = 'Value must be greater than or equal to "100".';
        $messageLessThan = 'Value must be less than "100".';
        $messageLessOrEqualThan = 'Value must be less than or equal to "100".';

        return [
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

            'incorrect data set type' => [
                $incorrectDataSet,
                [new Compare(targetAttribute: 'test')],
                ['' => ['The attribute value returned from a custom data set must have a scalar type.']],
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

            'string === null' => [null, [new Compare('', operator: '===')], ['' => ['Value must be equal to "".']]],
            'integer === string' => ['100', [new Compare(100, operator: '===')], ['' => [$messageEqual]]],
            'integer === float' => [100.0, [new Compare(100, operator: '===')], ['' => [$messageEqual]]],

            [null, [new Compare(0)], ['' => ['Value must be equal to "0".']]],

            [101, [new Compare(100)], ['' => [$messageEqual]]],

            [101, [new Compare(100, operator: '===')], ['' => [$messageEqual]]],
            [
                ['attribute' => 100, 'number' => 101],
                ['number' => new Compare(null, 'attribute', operator: '===')],
                ['number' => [$messageEqual]],
            ],

            [100, [new Compare(100, operator: '!=')], ['' => [$messageNotEqual]]],
            ['100', [new Compare(100, operator: '!=')], ['' => [$messageNotEqual]]],
            [100.0, [new Compare(100, operator: '!=')], ['' => [$messageNotEqual]]],

            [100, [new Compare(100, operator: '!==')], ['' => [$messageNotEqual]]],

            [100, [new Compare(100, operator: '>')], ['' => [$messageGreaterThan]]],
            [99, [new Compare(100, operator: '>')], ['' => [$messageGreaterThan]]],

            [99, [new Compare(100, operator: '>=')], ['' => [$messageGreaterOrEqualThan]]],

            [100, [new Compare(100, operator: '<')], ['' => [$messageLessThan]]],
            [101, [new Compare(100, operator: '<')], ['' => [$messageLessThan]]],

            [101, [new Compare(100, operator: '<=')], ['' => [$messageLessOrEqualThan]]],
            [
                ['attribute' => 100, 'number' => 101],
                ['number' => new Compare(null, 'attribute', operator: '<=')],
                ['number' => [$messageLessOrEqualThan]],
            ],

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
                        '{targetAttribute}, target value or attribute - {targetValueOrAttribute}, value - {value}.',
                        operator: '===',
                    ),
                ],
                [
                    'number' => [
                        'Attribute - number, target value - , target attribute - attribute, target value or ' .
                        'attribute - 100, value - 101.',
                    ],
                ],
            ],

            ['100.50', [new Compare('100.5')], ['' => ['Value must be equal to "100.5".']]],
            ['100.50', [new Compare('100.5', operator: '===')], ['' => ['Value must be equal to "100.5".']]],
        ];
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
