<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule\Type;

use Yiisoft\Validator\Rule\Type\IntegerType;
use Yiisoft\Validator\Rule\Type\IntegerTypeHandler;
use Yiisoft\Validator\Tests\Rule\Base\DifferentRuleInHandlerTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\RuleTestCase;
use Yiisoft\Validator\Tests\Rule\Base\RuleWithOptionsTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\SkipOnErrorTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\WhenTestTrait;

final class IntegerTypeTest extends RuleTestCase
{
    use DifferentRuleInHandlerTestTrait;
    use RuleWithOptionsTestTrait;
    use SkipOnErrorTestTrait;
    use WhenTestTrait;

    public function testGetName(): void
    {
        $rule = new IntegerType();
        $this->assertSame('integerType', $rule->getName());
    }

    public function dataOptions(): array
    {
        return [
            'default' => [
                new IntegerType(),
                [
                    'message' => [
                        'template' => 'Value must be an integer.',
                        'parameters' => [],
                    ],
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            'custom' => [
                new IntegerType(message: 'Custom message.', skipOnError: true, skipOnEmpty: true, ),
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
            [-1, [new IntegerType()]],
            [0, [new IntegerType()]],
            [1, [new IntegerType()]],
        ];
    }

    public function dataValidationFailed(): array
    {
        $message = 'Value must be an integer.';

        return [
            [false, [new IntegerType()], ['' => [$message]]],
            [1.5, [new IntegerType()], ['' => [$message]]],
            ['1.5', [new IntegerType()], ['' => [$message]]],
            [[], [new IntegerType()], ['' => [$message]]],
        ];
    }

    public function testSkipOnError(): void
    {
        $this->testSkipOnErrorInternal(new IntegerType(), new IntegerType(skipOnError: true));
    }

    public function testWhen(): void
    {
        $when = static fn (mixed $value): bool => $value !== null;
        $this->testWhenInternal(new IntegerType(), new IntegerType(when: $when));
    }

    protected function getDifferentRuleInHandlerItems(): array
    {
        return [IntegerType::class, IntegerTypeHandler::class];
    }
}
