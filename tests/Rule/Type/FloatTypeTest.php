<?php

declare(strict_types=1);

namespace Rule\Type;

use Yiisoft\Validator\Rule\Type\FloatType;
use Yiisoft\Validator\Rule\Type\FloatTypeHandler;
use Yiisoft\Validator\Tests\Rule\Base\DifferentRuleInHandlerTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\RuleTestCase;
use Yiisoft\Validator\Tests\Rule\Base\RuleWithOptionsTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\SkipOnErrorTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\WhenTestTrait;

final class FloatTypeTest extends RuleTestCase
{
    use DifferentRuleInHandlerTestTrait;
    use RuleWithOptionsTestTrait;
    use SkipOnErrorTestTrait;
    use WhenTestTrait;

    public function testGetName(): void
    {
        $rule = new FloatType();
        $this->assertSame('floatType', $rule->getName());
    }

    public function dataOptions(): array
    {
        return [
            'default' => [
                new FloatType(),
                [
                    'message' => [
                        'template' => 'Value must be a float.',
                        'parameters' => [],
                    ],
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            'custom' => [
                new FloatType(message: 'Custom message.', skipOnError: true, skipOnEmpty: true, ),
                [
                    'message' => [
                        'template' => 'Custom message.',
                        'parameters' => [],
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
            [-1.5, [new FloatType()]],
            [0.0, [new FloatType()]],
            [1.5, [new FloatType()]],
        ];
    }

    public function dataValidationFailed(): array
    {
        $message = 'Value must be a float.';

        return [
            [false, [new FloatType()], ['' => [$message]]],
            [0, [new FloatType()], ['' => [$message]]],
            ['1.5', [new FloatType()], ['' => [$message]]],
            [[], [new FloatType()], ['' => [$message]]],
        ];
    }

    public function testSkipOnError(): void
    {
        $this->testSkipOnErrorInternal(new FloatType(), new FloatType(skipOnError: true));
    }

    public function testWhen(): void
    {
        $when = static fn (mixed $value): bool => $value !== null;
        $this->testWhenInternal(new FloatType(), new FloatType(when: $when));
    }

    protected function getDifferentRuleInHandlerItems(): array
    {
        return [FloatType::class, FloatTypeHandler::class];
    }
}
