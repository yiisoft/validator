<?php

declare(strict_types=1);

namespace Rule\Type;

use Stringable;
use Yiisoft\Validator\Rule\Type\StringType;
use Yiisoft\Validator\Rule\Type\StringTypeHandler;
use Yiisoft\Validator\Tests\Rule\Base\DifferentRuleInHandlerTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\RuleTestCase;
use Yiisoft\Validator\Tests\Rule\Base\RuleWithOptionsTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\SkipOnErrorTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\WhenTestTrait;

final class StringTypeTest extends RuleTestCase
{
    use DifferentRuleInHandlerTestTrait;
    use RuleWithOptionsTestTrait;
    use SkipOnErrorTestTrait;
    use WhenTestTrait;

    public function testGetName(): void
    {
        $rule = new StringType();
        $this->assertSame('stringType', $rule->getName());
    }

    public function dataOptions(): array
    {
        return [
            'default' => [
                new StringType(),
                [
                    'message' => [
                        'template' => 'Value must be a string.',
                        'parameters' => [],
                    ],
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            'custom' => [
                new StringType(message: 'Custom message.', skipOnError: true, skipOnEmpty: true,),
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
            ['', [new StringType()]],
            ['test', [new StringType()]],
        ];
    }

    public function dataValidationFailed(): array
    {
        $message = 'Value must be a string.';

        return [
            [false, [new StringType()], ['' => [$message]]],
            [1.5, [new StringType()], ['' => [$message]]],
            [1, [new StringType()], ['' => [$message]]],
            [
                new class () implements Stringable
                {
                    public function __toString(): string
                    {
                        return 'test';
                    }
                },
                [new StringType()],
                ['' => [$message]],
            ],
            [[], [new StringType()], ['' => [$message]]],
        ];
    }

    public function testSkipOnError(): void
    {
        $this->testSkipOnErrorInternal(new StringType(), new StringType(skipOnError: true));
    }

    public function testWhen(): void
    {
        $when = static fn (mixed $value): bool => $value !== null;
        $this->testWhenInternal(new StringType(), new StringType(when: $when));
    }

    protected function getDifferentRuleInHandlerItems(): array
    {
        return [StringType::class, StringTypeHandler::class];
    }
}
