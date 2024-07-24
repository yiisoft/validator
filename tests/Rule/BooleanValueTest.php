<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use Yiisoft\Validator\Rule\BooleanValue;
use Yiisoft\Validator\Rule\BooleanValueHandler;
use Yiisoft\Validator\Tests\Rule\Base\DifferentRuleInHandlerTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\RuleTestCase;
use Yiisoft\Validator\Tests\Rule\Base\RuleWithOptionsTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\SkipOnErrorTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\WhenTestTrait;

final class BooleanValueTest extends RuleTestCase
{
    use DifferentRuleInHandlerTestTrait;
    use RuleWithOptionsTestTrait;
    use SkipOnErrorTestTrait;
    use WhenTestTrait;

    public function testGetName(): void
    {
        $rule = new BooleanValue();
        $this->assertSame(BooleanValue::class, $rule->getName());
    }

    public function dataOptions(): array
    {
        return [
            [
                new BooleanValue(),
                [
                    'trueValue' => '1',
                    'falseValue' => '0',
                    'strict' => false,
                    'incorrectInputMessage' => [
                        'template' => 'The allowed types are integer, float, string, boolean. {type} given.',
                        'parameters' => [
                            'true' => '1',
                            'false' => '0',
                        ],
                    ],
                    'message' => [
                        'template' => '{Property} must be either "{true}" or "{false}".',
                        'parameters' => [
                            'true' => '1',
                            'false' => '0',
                        ],
                    ],
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                new BooleanValue(trueValue: true, falseValue: false, strict: true),
                [
                    'trueValue' => true,
                    'falseValue' => false,
                    'strict' => true,
                    'incorrectInputMessage' => [
                        'template' => 'The allowed types are integer, float, string, boolean. {type} given.',
                        'parameters' => [
                            'true' => 'true',
                            'false' => 'false',
                        ],
                    ],
                    'message' => [
                        'template' => '{Property} must be either "{true}" or "{false}".',
                        'parameters' => [
                            'true' => 'true',
                            'false' => 'false',
                        ],
                    ],
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                new BooleanValue(
                    trueValue: 'YES',
                    falseValue: 'NO',
                    strict: true,
                    incorrectInputMessage: 'Custom message 1.',
                    message: 'Custom message 2.',
                    skipOnEmpty: true,
                    skipOnError: true
                ),
                [
                    'trueValue' => 'YES',
                    'falseValue' => 'NO',
                    'strict' => true,
                    'incorrectInputMessage' => [
                        'template' => 'Custom message 1.',
                        'parameters' => [
                            'true' => 'YES',
                            'false' => 'NO',
                        ],
                    ],
                    'message' => [
                        'template' => 'Custom message 2.',
                        'parameters' => [
                            'true' => 'YES',
                            'false' => 'NO',
                        ],
                    ],
                    'skipOnEmpty' => true,
                    'skipOnError' => true,
                ],
            ],
        ];
    }

    public function dataValidationPassed(): array
    {
        return [
            [true, [new BooleanValue()]],
            [false, [new BooleanValue()]],

            ['0', [new BooleanValue()]],
            ['1', [new BooleanValue()]],

            ['0', [new BooleanValue(strict: true)]],
            ['1', [new BooleanValue(strict: true)]],

            [true, [new BooleanValue(trueValue: true, falseValue: false, strict: true)]],
            [false, [new BooleanValue(trueValue: true, falseValue: false, strict: true)]],
        ];
    }

    public function dataValidationFailed(): array
    {
        $defaultErrors = ['' => ['Value must be either "1" or "0".']];
        $booleanErrors = ['' => ['Value must be either "true" or "false".']];

        return [
            ['5', [new BooleanValue()], $defaultErrors],

            [null, [new BooleanValue()], ['' => ['The allowed types are integer, float, string, boolean. null given.']]],
            [[], [new BooleanValue()], ['' => ['The allowed types are integer, float, string, boolean. array given.']]],

            [true, [new BooleanValue(strict: true)], $defaultErrors],
            [false, [new BooleanValue(strict: true)], $defaultErrors],

            ['0', [new BooleanValue(trueValue: true, falseValue: false, strict: true)], $booleanErrors],
            [
                [],
                [new BooleanValue(trueValue: true, falseValue: false, strict: true)],
                ['' => ['The allowed types are integer, float, string, boolean. array given.']],
            ],

            'custom message' => [
                5,
                [new BooleanValue(message: 'Custom error.')],
                ['' => ['Custom error.']],
            ],
            'custom message with parameters' => [
                5,
                [
                    new BooleanValue(
                        message: 'Property - {Property}, true - {true}, false - {false}, value - {value}.',
                    ),
                ],
                ['' => ['Property - Value, true - 1, false - 0, value - 5.']],
            ],
            'custom message with parameters, custom true and false values, strict' => [
                5,
                [
                    new BooleanValue(
                        trueValue: true,
                        falseValue: false,
                        strict: true,
                        message: 'Attribute - {property}, true - {true}, false - {false}, value - {value}.',
                    ),
                ],
                ['' => ['Attribute - value, true - true, false - false, value - 5.']],
            ],
            'custom message with parameters, attribute set' => [
                ['data' => 5],
                [
                    'data' => new BooleanValue(
                        message: 'Property - {Property}, true - {true}, false - {false}, value - {value}.',
                    ),
                ],
                ['data' => ['Property - Data, true - 1, false - 0, value - 5.']],
            ],
            'custom incorrect input message' => [
                [],
                [new BooleanValue(incorrectInputMessage: 'Custom error.')],
                ['' => ['Custom error.']],
            ],
            'custom incorrect input message with parameters' => [
                [],
                [
                    new BooleanValue(
                        incorrectInputMessage: 'Attribute - {property}, true - {true}, false - {false}, type - {type}.',
                    ),
                ],
                ['' => ['Attribute - value, true - 1, false - 0, type - array.']],
            ],
            'custom incorrect input message with parameters, custom true and false values, strict' => [
                [],
                [
                    new BooleanValue(
                        trueValue: true,
                        falseValue: false,
                        strict: true,
                        incorrectInputMessage: 'Attribute - {property}, true - {true}, false - {false}, type - {type}.',
                    ),
                ],
                ['' => ['Attribute - value, true - true, false - false, type - array.']],
            ],
            'custom incorrect input message with parameters, attribute set' => [
                ['data' => []],
                [
                    'data' => new BooleanValue(
                        incorrectInputMessage: 'Attribute - {property}, true - {true}, false - {false}, type - {type}.',
                    ),
                ],
                ['data' => ['Attribute - data, true - 1, false - 0, type - array.']],
            ],
            'custom incorrect input message, null' => [
                null,
                [
                    new BooleanValue(
                        incorrectInputMessage: 'Attribute - {property}, true - {true}, false - {false}, type - {type}.',
                    ),
                ],
                ['' => ['Attribute - value, true - 1, false - 0, type - null.']],
            ],
        ];
    }

    public function testSkipOnError(): void
    {
        $this->testSkipOnErrorInternal(new BooleanValue(), new BooleanValue(skipOnError: true));
    }

    public function testWhen(): void
    {
        $when = static fn (mixed $value): bool => $value !== null;
        $this->testWhenInternal(new BooleanValue(), new BooleanValue(when: $when));
    }

    protected function getDifferentRuleInHandlerItems(): array
    {
        return [BooleanValue::class, BooleanValueHandler::class];
    }
}
