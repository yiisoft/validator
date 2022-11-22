<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use InvalidArgumentException;
use stdClass;
use Yiisoft\Validator\DataSetInterface;
use Yiisoft\Validator\Rule\Compare;
use Yiisoft\Validator\Rule\CompareTo;
use Yiisoft\Validator\Tests\Rule\Base\RuleTestCase;
use Yiisoft\Validator\Tests\Rule\Base\SerializableRuleTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\SkipOnErrorTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\WhenTestTrait;

final class CompareToTest extends RuleTestCase
{
    use SerializableRuleTestTrait;
    use SkipOnErrorTestTrait;
    use WhenTestTrait;

    public function testInitWithWrongOperator(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $message = 'Operator "=" is not supported. The valid operators are: "==", "===", "!=", "!==", ">", ">=", ' .
            '"<", "<=".';
        $this->expectExceptionMessage($message);
        new CompareTo(operator: '=');
    }

    public function testGetName(): void
    {
        $rule = new CompareTo();
        $this->assertSame('compareTo', $rule->getName());
    }

    public function dataOptions(): array
    {
        return [
            [
                new CompareTo(1),
                [
                    'targetValue' => 1,
                    'targetAttribute' => null,
                    'incorrectInputMessage' => [
                        'message' => 'The allowed types are integer, float, string, boolean and null.',
                        'parameters' => [
                            'targetValue' => 1,
                            'targetAttribute' => null,
                            'targetValueOrAttribute' => 1,
                        ],
                    ],
                    'incorrectDataSetTypeMessage' => [
                        'message' => 'The attribute value returned from a custom data set must have a scalar type.',
                        'parameters' => [
                            'targetValue' => 1,
                            'targetAttribute' => null,
                            'targetValueOrAttribute' => 1,
                        ],
                    ],
                    'message' => [
                        'message' => 'Value must be equal to "{targetValueOrAttribute}".',
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
                new CompareTo(1, type: CompareTo::TYPE_NUMBER),
                [
                    'targetValue' => 1,
                    'targetAttribute' => null,
                    'incorrectInputMessage' => [
                        'message' => 'The allowed types are integer, float, string, boolean and null.',
                        'parameters' => [
                            'targetValue' => 1,
                            'targetAttribute' => null,
                            'targetValueOrAttribute' => 1,
                        ],
                    ],
                    'incorrectDataSetTypeMessage' => [
                        'message' => 'The attribute value returned from a custom data set must have a scalar type.',
                        'parameters' => [
                            'targetValue' => 1,
                            'targetAttribute' => null,
                            'targetValueOrAttribute' => 1,
                        ],
                    ],
                    'message' => [
                        'message' => 'Value must be equal to "{targetValueOrAttribute}".',
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
                new CompareTo(1, type: CompareTo::TYPE_NUMBER, operator: '>='),
                [
                    'targetValue' => 1,
                    'targetAttribute' => null,
                    'incorrectInputMessage' => [
                        'message' => 'The allowed types are integer, float, string, boolean and null.',
                        'parameters' => [
                            'targetValue' => 1,
                            'targetAttribute' => null,
                            'targetValueOrAttribute' => 1,
                        ],
                    ],
                    'incorrectDataSetTypeMessage' => [
                        'message' => 'The attribute value returned from a custom data set must have a scalar type.',
                        'parameters' => [
                            'targetValue' => 1,
                            'targetAttribute' => null,
                            'targetValueOrAttribute' => 1,
                        ],
                    ],
                    'message' => [
                        'message' => 'Value must be greater than or equal to "{targetValueOrAttribute}".',
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
                new CompareTo('YES'),
                [
                    'targetValue' => 'YES',
                    'targetAttribute' => null,
                    'incorrectInputMessage' => [
                        'message' => 'The allowed types are integer, float, string, boolean and null.',
                        'parameters' => [
                            'targetValue' => 'YES',
                            'targetAttribute' => null,
                            'targetValueOrAttribute' => 'YES',
                        ],
                    ],
                    'incorrectDataSetTypeMessage' => [
                        'message' => 'The attribute value returned from a custom data set must have a scalar type.',
                        'parameters' => [
                            'targetValue' => 'YES',
                            'targetAttribute' => null,
                            'targetValueOrAttribute' => 'YES',
                        ],
                    ],
                    'message' => [
                        'message' => 'Value must be equal to "{targetValueOrAttribute}".',
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
                new CompareTo('YES', skipOnEmpty: true),
                [
                    'targetValue' => 'YES',
                    'targetAttribute' => null,
                    'incorrectInputMessage' => [
                        'message' => 'The allowed types are integer, float, string, boolean and null.',
                        'parameters' => [
                            'targetValue' => 'YES',
                            'targetAttribute' => null,
                            'targetValueOrAttribute' => 'YES',
                        ],
                    ],
                    'incorrectDataSetTypeMessage' => [
                        'message' => 'The attribute value returned from a custom data set must have a scalar type.',
                        'parameters' => [
                            'targetValue' => 'YES',
                            'targetAttribute' => null,
                            'targetValueOrAttribute' => 'YES',
                        ],
                    ],
                    'message' => [
                        'message' => 'Value must be equal to "{targetValueOrAttribute}".',
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
                new CompareTo('YES', operator: '!=='),
                [
                    'targetValue' => 'YES',
                    'targetAttribute' => null,
                    'incorrectInputMessage' => [
                        'message' => 'The allowed types are integer, float, string, boolean and null.',
                        'parameters' => [
                            'targetValue' => 'YES',
                            'targetAttribute' => null,
                            'targetValueOrAttribute' => 'YES',
                        ],
                    ],
                    'incorrectDataSetTypeMessage' => [
                        'message' => 'The attribute value returned from a custom data set must have a scalar type.',
                        'parameters' => [
                            'targetValue' => 'YES',
                            'targetAttribute' => null,
                            'targetValueOrAttribute' => 'YES',
                        ],
                    ],
                    'message' => [
                        'message' => 'Value must not be equal to "{targetValueOrAttribute}".',
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
                new CompareTo('YES', message: 'Custom message for {targetValueOrAttribute}.'),
                [
                    'targetValue' => 'YES',
                    'targetAttribute' => null,
                    'incorrectInputMessage' => [
                        'message' => 'The allowed types are integer, float, string, boolean and null.',
                        'parameters' => [
                            'targetValue' => 'YES',
                            'targetAttribute' => null,
                            'targetValueOrAttribute' => 'YES',
                        ],
                    ],
                    'incorrectDataSetTypeMessage' => [
                        'message' => 'The attribute value returned from a custom data set must have a scalar type.',
                        'parameters' => [
                            'targetValue' => 'YES',
                            'targetAttribute' => null,
                            'targetValueOrAttribute' => 'YES',
                        ],
                    ],
                    'message' => [
                        'message' => 'Custom message for {targetValueOrAttribute}.',
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
                new CompareTo(null, 'test'),
                [
                    'targetValue' => null,
                    'targetAttribute' => 'test',
                    'incorrectInputMessage' => [
                        'message' => 'The allowed types are integer, float, string, boolean and null.',
                        'parameters' => [
                            'targetValue' => null,
                            'targetAttribute' => 'test',
                            'targetValueOrAttribute' => 'test',
                        ],
                    ],
                    'incorrectDataSetTypeMessage' => [
                        'message' => 'The attribute value returned from a custom data set must have a scalar type.',
                        'parameters' => [
                            'targetValue' => null,
                            'targetAttribute' => 'test',
                            'targetValueOrAttribute' => 'test',
                        ],
                    ],
                    'message' => [
                        'message' => 'Value must be equal to "{targetValueOrAttribute}".',
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
                new CompareTo(
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
                        'message' => 'Custom message 1.',
                        'parameters' => [
                            'targetValue' => null,
                            'targetAttribute' => 'test',
                            'targetValueOrAttribute' => 'test',
                        ],
                    ],
                    'incorrectDataSetTypeMessage' => [
                        'message' => 'Custom message 2.',
                        'parameters' => [
                            'targetValue' => null,
                            'targetAttribute' => 'test',
                            'targetValueOrAttribute' => 'test',
                        ],
                    ],
                    'message' => [
                        'message' => 'Custom message 3.',
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
                new CompareTo(1, 'test'),
                [
                    'targetValue' => 1,
                    'targetAttribute' => 'test',
                    'incorrectInputMessage' => [
                        'message' => 'The allowed types are integer, float, string, boolean and null.',
                        'parameters' => [
                            'targetValue' => 1,
                            'targetAttribute' => 'test',
                            'targetValueOrAttribute' => 1,
                        ],
                    ],
                    'incorrectDataSetTypeMessage' => [
                        'message' => 'The attribute value returned from a custom data set must have a scalar type.',
                        'parameters' => [
                            'targetValue' => 1,
                            'targetAttribute' => 'test',
                            'targetValueOrAttribute' => 1,
                        ],
                    ],
                    'message' => [
                        'message' => 'Value must be equal to "{targetValueOrAttribute}".',
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
            [null, [new CompareTo('')]],
            [null, [new CompareTo('', operator: '===')]],

            [100, [new CompareTo(100)]],
            [['attribute' => 100, 'number' => 100], ['number' => new CompareTo(null, 'attribute')]],
            ['100', [new CompareTo(100)]],

            [100, [new CompareTo(100, operator: '===')]],
            ['100', [new CompareTo(100, operator: '===')]],
            [100.0, [new CompareTo(100, operator: '===')]],

            [100.00001, [new CompareTo(100, operator: '!=')]],
            [false, [new CompareTo(100, operator: '!=')]],

            [false, [new CompareTo(100, operator: '!==')]],

            [101, [new CompareTo(100, operator: '>')]],

            [100, [new CompareTo(100, operator: '>=')]],
            [101, [new CompareTo(100, operator: '>=')]],
            [99, [new CompareTo(100, operator: '<')]],

            [100, [new CompareTo(100, operator: '<=')]],
            [99, [new CompareTo(100, operator: '<=')]],
            [['attribute' => 100, 'number' => 99], ['number' => new CompareTo(null, 'attribute', operator: '<=')]],

            ['100.50', [new CompareTo('100.5', type: Compare::TYPE_NUMBER)]],
            ['100.50', [new CompareTo('100.5', type: Compare::TYPE_NUMBER, operator: '===')]],
        ];
    }

    public function dataValidationFailed(): array
    {
        $incorrectDataSet = new class () implements DataSetInterface {
            public function getAttributeValue(string $attribute): mixed
            {
                return new stdClass();
            }

            public function getData(): mixed
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
                [new CompareTo(false)],
                ['' => ['The allowed types are integer, float, string, boolean and null.']],
            ],
            'custom incorrect input message' => [
                [],
                [new CompareTo(false, incorrectInputMessage: 'Custom incorrect input message.')],
                ['' => ['Custom incorrect input message.']],
            ],
            'custom incorrect input message with parameters' => [
                [],
                [new CompareTo(false, incorrectInputMessage: 'Attribute - {attribute}, type - {type}.')],
                ['' => ['Attribute - , type - array.']],
            ],
            'custom incorrect input message with parameters, attribute set' => [
                ['data' => []],
                ['data' => new CompareTo(false, incorrectInputMessage: 'Attribute - {attribute}, type - {type}.')],
                ['data' => ['Attribute - data, type - array.']],
            ],

            'incorrect data set type' => [
                $incorrectDataSet,
                [new CompareTo(targetAttribute: 'test')],
                ['' => ['The attribute value returned from a custom data set must have a scalar type.']],
            ],
            'custom incorrect data set type message' => [
                $incorrectDataSet,
                [
                    new CompareTo(
                        targetAttribute: 'test',
                        incorrectDataSetTypeMessage: 'Custom incorrect data set type message.',
                    ),
                ],
                ['' => ['Custom incorrect data set type message.']],
            ],
            'custom incorrect data set type message with parameters' => [
                $incorrectDataSet,
                [
                    new CompareTo(
                        targetAttribute: 'test',
                        incorrectDataSetTypeMessage: 'Type - {type}.',
                    ),
                ],
                ['' => ['Type - stdClass.']],
            ],

            [null, [new CompareTo(0)], ['' => ['Value must be equal to "0".']]],

            [101, [new CompareTo(100)], ['' => [$messageEqual]]],

            [101, [new CompareTo(100, operator: '===')], ['' => [$messageEqual]]],
            [
                ['attribute' => 100, 'number' => 101],
                ['number' => new CompareTo(null, 'attribute', operator: '===')],
                ['number' => [$messageEqual]],
            ],

            [100, [new CompareTo(100, operator: '!=')], ['' => [$messageNotEqual]]],
            ['100', [new CompareTo(100, operator: '!=')], ['' => [$messageNotEqual]]],
            [100.0, [new CompareTo(100, operator: '!=')], ['' => [$messageNotEqual]]],

            [100, [new CompareTo(100, operator: '!==')], ['' => [$messageNotEqual]]],
            ['100', [new CompareTo(100, operator: '!==')], ['' => [$messageNotEqual]]],
            [100.0, [new CompareTo(100, operator: '!==')], ['' => [$messageNotEqual]]],

            [100, [new CompareTo(100, operator: '>')], ['' => [$messageGreaterThan]]],
            [99, [new CompareTo(100, operator: '>')], ['' => [$messageGreaterThan]]],

            [99, [new CompareTo(100, operator: '>=')], ['' => [$messageGreaterOrEqualThan]]],

            [100, [new CompareTo(100, operator: '<')], ['' => [$messageLessThan]]],
            [101, [new CompareTo(100, operator: '<')], ['' => [$messageLessThan]]],

            [101, [new CompareTo(100, operator: '<=')], ['' => [$messageLessOrEqualThan]]],
            [
                ['attribute' => 100, 'number' => 101],
                ['number' => new CompareTo(null, 'attribute', operator: '<=')],
                ['number' => [$messageLessOrEqualThan]],
            ],

            'custom message' => [101, [new CompareTo(100, message: 'Custom message.')], ['' => ['Custom message.']]],

            ['100.50', [new CompareTo('100.5', operator: '===')], ['' => ['Value must be equal to "100.5".']]],
        ];
    }

    public function testSkipOnError(): void
    {
        $this->testSkipOnErrorInternal(new CompareTo(), new CompareTo(skipOnError: true));
    }

    public function testWhen(): void
    {
        $when = static fn (mixed $value): bool => $value !== null;
        $this->testWhenInternal(new CompareTo(), new CompareTo(when: $when));
    }
}
