<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use Yiisoft\Validator\Rule\BoolValue;
use Yiisoft\Validator\Rule\BoolValueHandler;
use Yiisoft\Validator\Tests\Rule\Base\DifferentRuleInHandlerTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\RuleTestCase;
use Yiisoft\Validator\Tests\Rule\Base\RuleWithOptionsTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\SkipOnErrorTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\WhenTestTrait;

final class BoolValueTest extends RuleTestCase
{
    use DifferentRuleInHandlerTestTrait;
    use RuleWithOptionsTestTrait;
    use SkipOnErrorTestTrait;
    use WhenTestTrait;

    public function testGetName(): void
    {
        $rule = new BoolValue();
        $this->assertSame('boolean', $rule->getName());
    }

    public function dataOptions(): array
    {
        return [
            [
                new BoolValue(),
                [
                    'trueValue' => '1',
                    'falseValue' => '0',
                    'strict' => false,
                    'incorrectInputMessage' => [
                        'template' => 'Value must be either "{true}" or "{false}".',
                        'parameters' => [
                            'true' => '1',
                            'false' => '0',
                        ],
                    ],
                    'message' => [
                        'template' => 'Value must be either "{true}" or "{false}".',
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
                new BoolValue(trueValue: true, falseValue: false, strict: true),
                [
                    'trueValue' => true,
                    'falseValue' => false,
                    'strict' => true,
                    'incorrectInputMessage' => [
                        'template' => 'Value must be either "{true}" or "{false}".',
                        'parameters' => [
                            'true' => 'true',
                            'false' => 'false',
                        ],
                    ],
                    'message' => [
                        'template' => 'Value must be either "{true}" or "{false}".',
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
                new BoolValue(
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
            [true, [new BoolValue()]],
            [false, [new BoolValue()]],

            ['0', [new BoolValue()]],
            ['1', [new BoolValue()]],

            ['0', [new BoolValue(strict: true)]],
            ['1', [new BoolValue(strict: true)]],

            [true, [new BoolValue(trueValue: true, falseValue: false, strict: true)]],
            [false, [new BoolValue(trueValue: true, falseValue: false, strict: true)]],
        ];
    }

    public function dataValidationFailed(): array
    {
        $defaultErrors = ['' => ['Value must be either "1" or "0".']];
        $booleanErrors = ['' => ['Value must be either "true" or "false".']];

        return [
            ['5', [new BoolValue()], $defaultErrors],

            [null, [new BoolValue()], $defaultErrors],
            [[], [new BoolValue()], $defaultErrors],

            [true, [new BoolValue(strict: true)], $defaultErrors],
            [false, [new BoolValue(strict: true)], $defaultErrors],

            ['0', [new BoolValue(trueValue: true, falseValue: false, strict: true)], $booleanErrors],
            [[], [new BoolValue(trueValue: true, falseValue: false, strict: true)], $booleanErrors],

            'custom message' => [
                5,
                [new BoolValue(message: 'Custom error.')],
                ['' => ['Custom error.']],
            ],
            'custom message with parameters' => [
                5,
                [
                    new BoolValue(
                        message: 'Attribute - {attribute}, true - {true}, false - {false}, value - {value}.',
                    ),
                ],
                ['' => ['Attribute - , true - 1, false - 0, value - 5.']],
            ],
            'custom message with parameters, custom true and false values, strict' => [
                5,
                [
                    new BoolValue(
                        trueValue: true,
                        falseValue: false,
                        strict: true,
                        message: 'Attribute - {attribute}, true - {true}, false - {false}, value - {value}.',
                    ),
                ],
                ['' => ['Attribute - , true - true, false - false, value - 5.']],
            ],
            'custom message with parameters, attribute set' => [
                ['data' => 5],
                [
                    'data' => new BoolValue(
                        message: 'Attribute - {attribute}, true - {true}, false - {false}, value - {value}.',
                    ),
                ],
                ['data' => ['Attribute - data, true - 1, false - 0, value - 5.']],
            ],
            'custom incorrect input message' => [
                [],
                [new BoolValue(incorrectInputMessage: 'Custom error.')],
                ['' => ['Custom error.']],
            ],
            'custom incorrect input message with parameters' => [
                [],
                [
                    new BoolValue(
                        incorrectInputMessage: 'Attribute - {attribute}, true - {true}, false - {false}, type - {type}.',
                    ),
                ],
                ['' => ['Attribute - , true - 1, false - 0, type - array.']],
            ],
            'custom incorrect input message with parameters, custom true and false values, strict' => [
                [],
                [
                    new BoolValue(
                        trueValue: true,
                        falseValue: false,
                        strict: true,
                        incorrectInputMessage: 'Attribute - {attribute}, true - {true}, false - {false}, type - {type}.',
                    ),
                ],
                ['' => ['Attribute - , true - true, false - false, type - array.']],
            ],
            'custom incorrect input message with parameters, attribute set' => [
                ['data' => []],
                [
                    'data' => new BoolValue(
                        incorrectInputMessage: 'Attribute - {attribute}, true - {true}, false - {false}, type - {type}.',
                    ),
                ],
                ['data' => ['Attribute - data, true - 1, false - 0, type - array.']],
            ],
            'custom incorrect input message, null' => [
                null,
                [
                    new BoolValue(
                        incorrectInputMessage: 'Attribute - {attribute}, true - {true}, false - {false}, type - {type}.',
                    ),
                ],
                ['' => ['Attribute - , true - 1, false - 0, type - null.']],
            ],
        ];
    }

    public function testSkipOnError(): void
    {
        $this->testSkipOnErrorInternal(new BoolValue(), new BoolValue(skipOnError: true));
    }

    public function testWhen(): void
    {
        $when = static fn (mixed $value): bool => $value !== null;
        $this->testWhenInternal(new BoolValue(), new BoolValue(when: $when));
    }

    protected function getDifferentRuleInHandlerItems(): array
    {
        return [BoolValue::class, BoolValueHandler::class];
    }
}
