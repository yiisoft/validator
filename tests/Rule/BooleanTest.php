<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use Yiisoft\Validator\Rule\Boolean;
use Yiisoft\Validator\Rule\BooleanHandler;
use Yiisoft\Validator\Tests\Rule\Base\DifferentRuleInHandlerTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\RuleTestCase;
use Yiisoft\Validator\Tests\Rule\Base\SerializableRuleTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\SkipOnErrorTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\WhenTestTrait;

final class BooleanTest extends RuleTestCase
{
    use DifferentRuleInHandlerTestTrait;
    use SerializableRuleTestTrait;
    use SkipOnErrorTestTrait;
    use WhenTestTrait;

    public function testGetName(): void
    {
        $rule = new Boolean();
        $this->assertSame('boolean', $rule->getName());
    }

    public function dataOptions(): array
    {
        return [
            [
                new Boolean(),
                [
                    'trueValue' => '1',
                    'falseValue' => '0',
                    'strict' => false,
                    'nonScalarMessage' => [
                        'message' => 'Value must be either "{true}" or "{false}".',
                        'parameters' => [
                            'true' => '1',
                            'false' => '0',
                        ],
                    ],
                    'scalarMessage' => [
                        'message' => 'Value must be either "{true}" or "{false}".',
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
                new Boolean(trueValue: true, falseValue: false, strict: true),
                [
                    'trueValue' => true,
                    'falseValue' => false,
                    'strict' => true,
                    'nonScalarMessage' => [
                        'message' => 'Value must be either "{true}" or "{false}".',
                        'parameters' => [
                            'true' => 'true',
                            'false' => 'false',
                        ],
                    ],
                    'scalarMessage' => [
                        'message' => 'Value must be either "{true}" or "{false}".',
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
                new Boolean(
                    trueValue: 'YES',
                    falseValue: 'NO',
                    strict: true,
                    nonScalarMessage: 'Custom message 1.',
                    scalarMessage: 'Custom message 2.',
                    skipOnEmpty: true,
                    skipOnError: true
                ),
                [
                    'trueValue' => 'YES',
                    'falseValue' => 'NO',
                    'strict' => true,
                    'nonScalarMessage' => [
                        'message' => 'Custom message 1.',
                        'parameters' => [
                            'true' => 'YES',
                            'false' => 'NO',
                        ],
                    ],
                    'scalarMessage' => [
                        'message' => 'Custom message 2.',
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
            [true, [new Boolean()]],
            [false, [new Boolean()]],

            ['0', [new Boolean()]],
            ['1', [new Boolean()]],

            ['0', [new Boolean(strict: true)]],
            ['1', [new Boolean(strict: true)]],

            [true, [new Boolean(trueValue: true, falseValue: false, strict: true)]],
            [false, [new Boolean(trueValue: true, falseValue: false, strict: true)]],
        ];
    }

    public function dataValidationFailed(): array
    {
        $defaultErrors = ['' => ['Value must be either "1" or "0".']];
        $booleanErrors = ['' => ['Value must be either "true" or "false".']];

        return [
            ['5', [new Boolean()], $defaultErrors],

            [null, [new Boolean()], $defaultErrors],
            [[], [new Boolean()], $defaultErrors],

            [true, [new Boolean(strict: true)], $defaultErrors],
            [false, [new Boolean(strict: true)], $defaultErrors],

            ['0', [new Boolean(trueValue: true, falseValue: false, strict: true)], $booleanErrors],
            [[], [new Boolean(trueValue: true, falseValue: false, strict: true)], $booleanErrors],

            'custom scalar message' => [5, [new Boolean(scalarMessage: 'Custom error.')], ['' => ['Custom error.']]],
            'custom scalar message with parameters' => [
                5,
                [
                    new Boolean(
                        scalarMessage: 'Attribute - {attribute}, true - {true}, false - {false}, value - {value}.',
                    ),
                ],
                ['' => ['Attribute - , true - 1, false - 0, value - 5.']],
            ],
            'custom scalar message with parameters, custom true and false values, strict' => [
                5,
                [
                    new Boolean(
                        trueValue: true,
                        falseValue: false,
                        strict: true,
                        scalarMessage: 'Attribute - {attribute}, true - {true}, false - {false}, value - {value}.',
                    ),
                ],
                ['' => ['Attribute - , true - true, false - false, value - 5.']],
            ],
            'custom scalar message with parameters, attribute set' => [
                ['data' => 5],
                [
                    'data' => new Boolean(
                        scalarMessage: 'Attribute - {attribute}, true - {true}, false - {false}, value - {value}.',
                    ),
                ],
                ['data' => ['Attribute - data, true - 1, false - 0, value - 5.']],
            ],
            'custom non-scalar message' => [
                [],
                [new Boolean(nonScalarMessage: 'Custom error.')],
                ['' => ['Custom error.']],
            ],
            'custom non-scalar message with parameters' => [
                [],
                [
                    new Boolean(
                        nonScalarMessage: 'Attribute - {attribute}, true - {true}, false - {false}, type - {type}.',
                    ),
                ],
                ['' => ['Attribute - , true - 1, false - 0, type - array.']],
            ],
            'custom non-scalar message with parameters, custom true and false values, strict' => [
                [],
                [
                    new Boolean(
                        trueValue: true,
                        falseValue: false,
                        strict: true,
                        nonScalarMessage: 'Attribute - {attribute}, true - {true}, false - {false}, type - {type}.',
                    ),
                ],
                ['' => ['Attribute - , true - true, false - false, type - array.']],
            ],
            'custom non-scalar message with parameters, attribute set' => [
                ['data' => []],
                [
                    'data' => new Boolean(
                        nonScalarMessage: 'Attribute - {attribute}, true - {true}, false - {false}, type - {type}.',
                    ),
                ],
                ['data' => ['Attribute - data, true - 1, false - 0, type - array.']],
            ],
        ];
    }

    public function testSkipOnError(): void
    {
        $this->testSkipOnErrorInternal(new Boolean(), new Boolean(skipOnError: true));
    }

    public function testWhen(): void
    {
        $when = static fn (mixed $value): bool => $value !== null;
        $this->testWhenInternal(new Boolean(), new Boolean(when: $when));
    }

    protected function getDifferentRuleInHandlerItems(): array
    {
        return [Boolean::class, BooleanHandler::class];
    }
}
