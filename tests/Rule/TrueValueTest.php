<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use Yiisoft\Validator\Rule\TrueValue;
use Yiisoft\Validator\Rule\TrueValueHandler;
use Yiisoft\Validator\Tests\Rule\Base\DifferentRuleInHandlerTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\RuleTestCase;
use Yiisoft\Validator\Tests\Rule\Base\RuleWithOptionsTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\SkipOnErrorTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\WhenTestTrait;

final class TrueValueTest extends RuleTestCase
{
    use DifferentRuleInHandlerTestTrait;
    use RuleWithOptionsTestTrait;
    use SkipOnErrorTestTrait;
    use WhenTestTrait;

    public function testGetName(): void
    {
        $rule = new TrueValue();
        $this->assertSame('isTrue', $rule->getName());
    }

    public function dataOptions(): array
    {
        return [
            [
                new TrueValue(),
                [
                    'trueValue' => '1',
                    'strict' => false,
                    'messageWithType' => [
                        'template' => 'The value must be "{true}".',
                        'parameters' => [
                            'true' => '1',
                        ],
                    ],
                    'messageWithValue' => [
                        'template' => 'The value must be "{true}".',
                        'parameters' => [
                            'true' => '1',
                        ],
                    ],
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                new TrueValue(trueValue: true, strict: true),
                [
                    'trueValue' => true,
                    'strict' => true,
                    'messageWithType' => [
                        'template' => 'The value must be "{true}".',
                        'parameters' => [
                            'true' => 'true',
                        ],
                    ],
                    'messageWithValue' => [
                        'template' => 'The value must be "{true}".',
                        'parameters' => [
                            'true' => 'true',
                        ],
                    ],
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                new TrueValue(
                    trueValue: 'YES',
                    strict: true,
                    incorrectInputMessage: 'Custom incorrect input message.',
                    message: 'Custom message.',
                    skipOnEmpty: true,
                    skipOnError: true
                ),
                [
                    'trueValue' => 'YES',
                    'strict' => true,
                    'messageWithType' => [
                        'template' => 'Custom incorrect input message.',
                        'parameters' => [
                            'true' => 'YES',
                        ],
                    ],
                    'messageWithValue' => [
                        'template' => 'Custom message.',
                        'parameters' => [
                            'true' => 'YES',
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
            [true, [new TrueValue()]],
            ['1', [new TrueValue()]],
            ['1', [new TrueValue(strict: true)]],
            [true, [new TrueValue(trueValue: true, strict: true)]],
        ];
    }

    public function dataValidationFailed(): array
    {
        return [
            ['5', [new TrueValue()], ['' => ['The value must be "1".']]],
            [null, [new TrueValue()], ['' => ['The value must be "1".']]],
            [[], [new TrueValue()], ['' => ['The value must be "1".']]],
            [true, [new TrueValue(strict: true)], ['' => ['The value must be "1".']]],
            ['1', [new TrueValue(trueValue: true, strict: true)], ['' => ['The value must be "true".']]],
            [[], [new TrueValue(trueValue: true, strict: true)], ['' => ['The value must be "true".']]],

            [false, [new TrueValue()], ['' => ['The value must be "1".']]],
            ['0', [new TrueValue()], ['' => ['The value must be "1".']]],
            ['0', [new TrueValue(strict: true)], ['' => ['The value must be "1".']]],
            [false, [new TrueValue(trueValue: true, strict: true)], ['' => ['The value must be "true".']]],

            'custom message' => [
                5,
                [new TrueValue(message: 'Custom error.')],
                ['' => ['Custom error.']],
            ],
            'custom message with parameters' => [
                5,
                [new TrueValue(message: 'Attribute - {attribute}, true - {true}, value - {value}.')],
                ['' => ['Attribute - , true - 1, value - 5.']],
            ],
            'custom message with parameters, custom true value, strict' => [
                5,
                [
                    new TrueValue(
                        trueValue: true,
                        strict: true,
                        message: 'Attribute - {attribute}, true - {true}, value - {value}.',
                    ),
                ],
                ['' => ['Attribute - , true - true, value - 5.']],
            ],
            'custom message with parameters, attribute set' => [
                ['data' => 5],
                [
                    'data' => new TrueValue(message: 'Attribute - {attribute}, true - {true}, value - {value}.'),
                ],
                ['data' => ['Attribute - data, true - 1, value - 5.']],
            ],
            'custom incorrect input message' => [
                [],
                [new TrueValue(incorrectInputMessage: 'Custom error.')],
                ['' => ['Custom error.']],
            ],
            'custom incorrect input message with parameters' => [
                [],
                [
                    new TrueValue(incorrectInputMessage: 'Attribute - {attribute}, true - {true}, type - {type}.'),
                ],
                ['' => ['Attribute - , true - 1, type - array.']],
            ],
            'custom incorrect input message with parameters, custom true and false values, strict' => [
                [],
                [
                    new TrueValue(
                        trueValue: true,
                        strict: true,
                        incorrectInputMessage: 'Attribute - {attribute}, true - {true}, type - {type}.',
                    ),
                ],
                ['' => ['Attribute - , true - true, type - array.']],
            ],
            'custom incorrect input message with parameters, attribute set' => [
                ['data' => []],
                [
                    'data' => new TrueValue(incorrectInputMessage: 'Attribute - {attribute}, true - {true}, type - {type}.'),
                ],
                ['data' => ['Attribute - data, true - 1, type - array.']],
            ],
            'custom incorrect input message, null' => [
                null,
                [new TrueValue(incorrectInputMessage: 'Attribute - {attribute}, true - {true}, type - {type}.'),],
                ['' => ['Attribute - , true - 1, type - null.']],
            ],
        ];
    }

    public function testSkipOnError(): void
    {
        $this->testSkipOnErrorInternal(new TrueValue(), new TrueValue(skipOnError: true));
    }

    public function testWhen(): void
    {
        $when = static fn (mixed $value): bool => $value !== null;
        $this->testWhenInternal(new TrueValue(), new TrueValue(when: $when));
    }

    protected function getDifferentRuleInHandlerItems(): array
    {
        return [TrueValue::class, TrueValueHandler::class];
    }
}
