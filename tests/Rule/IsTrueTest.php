<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use Yiisoft\Validator\Rule\IsTrue;
use Yiisoft\Validator\Rule\IsTrueHandler;
use Yiisoft\Validator\Tests\Rule\Base\DifferentRuleInHandlerTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\RuleTestCase;
use Yiisoft\Validator\Tests\Rule\Base\SerializableRuleTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\SkipOnErrorTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\WhenTestTrait;

final class IsTrueTest extends RuleTestCase
{
    use DifferentRuleInHandlerTestTrait;
    use SerializableRuleTestTrait;
    use SkipOnErrorTestTrait;
    use WhenTestTrait;

    public function testGetName(): void
    {
        $rule = new IsTrue();
        $this->assertSame('isTrue', $rule->getName());
    }

    public function dataOptions(): array
    {
        return [
            [
                new IsTrue(),
                [
                    'trueValue' => '1',
                    'strict' => false,
                    'message' => [
                        'message' => 'The value must be "{true}".',
                        'parameters' => [
                            'true' => '1',
                        ],
                    ],
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                new IsTrue(trueValue: true, strict: true),
                [
                    'trueValue' => true,
                    'strict' => true,
                    'message' => [
                        'message' => 'The value must be "{true}".',
                        'parameters' => [
                            'true' => 'true',
                        ],
                    ],
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                new IsTrue(
                    trueValue: 'YES',
                    strict: true,
                    message: 'Custom message.',
                    skipOnEmpty: true,
                    skipOnError: true
                ),
                [
                    'trueValue' => 'YES',
                    'strict' => true,
                    'message' => [
                        'message' => 'Custom message.',
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
            [true, [new IsTrue()]],
            ['1', [new IsTrue()]],
            ['1', [new IsTrue(strict: true)]],
            [true, [new IsTrue(trueValue: true, strict: true)]],
        ];
    }

    public function dataValidationFailed(): array
    {
        return [
            ['5', [new IsTrue()], ['' => ['The value must be "1".']]],
            [null, [new IsTrue()], ['' => ['The value must be "1".']]],
            [[], [new IsTrue()], ['' => ['The value must be "1".']]],
            [true, [new IsTrue(strict: true)], ['' => ['The value must be "1".']]],
            ['1', [new IsTrue(trueValue: true, strict: true)], ['' => ['The value must be "true".']]],
            [[], [new IsTrue(trueValue: true, strict: true)], ['' => ['The value must be "true".']]],

            [false, [new IsTrue()], ['' => ['The value must be "1".']]],
            ['0', [new IsTrue()], ['' => ['The value must be "1".']]],
            ['0', [new IsTrue(strict: true)], ['' => ['The value must be "1".']]],
            [false, [new IsTrue(trueValue: true, strict: true)], ['' => ['The value must be "true".']]],
            'custom error' => [5, [new IsTrue(message: 'Custom error.')], ['' => ['Custom error.']]],
        ];
    }

    public function testSkipOnError(): void
    {
        $this->testSkipOnErrorInternal(new IsTrue(), new IsTrue(skipOnError: true));
    }

    public function testWhen(): void
    {
        $when = static fn (mixed $value): bool => $value !== null;
        $this->testWhenInternal(new IsTrue(), new IsTrue(when: $when));
    }

    protected function getDifferentRuleInHandlerItems(): array
    {
        return [IsTrue::class, IsTrueHandler::class];
    }
}
