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
use Yiisoft\Validator\ValidationContext;

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
                    'message' => [
                        'message' => 'The value must be either "{true}" or "{false}".',
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
                    'message' => [
                        'message' => 'The value must be either "{true}" or "{false}".',
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
                    message: 'Custom message.',
                    skipOnEmpty: true,
                    skipOnError: true
                ),
                [
                    'trueValue' => 'YES',
                    'falseValue' => 'NO',
                    'strict' => true,
                    'message' => [
                        'message' => 'Custom message.',
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
        $defaultErrors = ['' => ['The value must be either "1" or "0".']];
        $booleanErrors = ['' => ['The value must be either "true" or "false".']];

        return [
            ['5', [new Boolean()], $defaultErrors],

            [null, [new Boolean()], $defaultErrors],
            [[], [new Boolean()], $defaultErrors],

            [true, [new Boolean(strict: true)], $defaultErrors],
            [false, [new Boolean(strict: true)], $defaultErrors],

            ['0', [new Boolean(trueValue: true, falseValue: false, strict: true)], $booleanErrors],
            [[], [new Boolean(trueValue: true, falseValue: false, strict: true)], $booleanErrors],

            'custom error' => [5, [new Boolean(message: 'Custom error.')], ['' => ['Custom error.']]],
        ];
    }

    public function testSkipOnError(): void
    {
        $this->testSkipOnErrorInternal(new Boolean(), new Boolean(skipOnError: true));
    }

    public function testWhen(): void
    {
        $when = static fn (mixed $value, ValidationContext $context): bool => $value !== null;
        $this->testWhenInternal(new Boolean(), new Boolean(when: $when));
    }

    protected function getDifferentRuleInHandlerItems(): array
    {
        return [Boolean::class, BooleanHandler::class];
    }
}
