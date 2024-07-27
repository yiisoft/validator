<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use Yiisoft\Validator\Rule\InEnum;
use Yiisoft\Validator\Rule\InEnumHandler;
use Yiisoft\Validator\Tests\Rule\Base\DifferentRuleInHandlerTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\RuleTestCase;
use Yiisoft\Validator\Tests\Rule\Base\RuleWithOptionsTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\SkipOnErrorTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\WhenTestTrait;
use Yiisoft\Validator\Tests\Support\Data\Enum\BackedEnumStatus;
use Yiisoft\Validator\Tests\Support\Data\Enum\EnumStatus;

final class InEnumTest extends RuleTestCase
{
    use DifferentRuleInHandlerTestTrait;
    use RuleWithOptionsTestTrait;
    use SkipOnErrorTestTrait;
    use WhenTestTrait;

    public function testGetName(): void
    {
        $rule = new InEnum(EnumStatus::class);
        $this->assertSame('inEnum', $rule->getName());
    }

    public function dataOptions(): array
    {
        $values = array_column(EnumStatus::class::cases(), 'name');

        return [
            [
                new InEnum(EnumStatus::class),
                [
                    'values' => $values,
                    'strict' => false,
                    'not' => false,
                    'message' => [
                        'template' => '{Property} is not in the list of acceptable values.',
                        'parameters' => [],
                    ],
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                new InEnum(EnumStatus::class, strict: true),
                [
                    'values' => $values,
                    'strict' => true,
                    'not' => false,
                    'message' => [
                        'template' => '{Property} is not in the list of acceptable values.',
                        'parameters' => [],
                    ],
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                new InEnum(EnumStatus::class, not: true),
                [
                    'values' => $values,
                    'strict' => false,
                    'not' => true,
                    'message' => [
                        'template' => '{Property} is not in the list of acceptable values.',
                        'parameters' => [],
                    ],
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
        ];
    }

    public function dataValidationPassed(): array
    {
        return [
            ['DRAFT', [new InEnum(EnumStatus::class)]],
            ['PUBLISHED', [new InEnum(EnumStatus::class)]],

            ['DRAFT', [new InEnum(BackedEnumStatus::class, useNames: true)]],
            ['PUBLISHED', [new InEnum(BackedEnumStatus::class, useNames: true)]],


            ['draft', [new InEnum(BackedEnumStatus::class)]],
            ['published', [new InEnum(BackedEnumStatus::class)]],
        ];
    }

    public function dataValidationFailed(): array
    {
        $errors = ['' => ['Value is not in the list of acceptable values.']];

        return [
            [
                '42',
                [new InEnum(EnumStatus::class)],
                $errors,
            ],
            [
                'DRAFT',
                [new InEnum(BackedEnumStatus::class)],
                $errors,
            ],
            [
                'draft',
                [new InEnum(BackedEnumStatus::class, useNames: true)],
                $errors,
            ],
        ];
    }

    public function testSkipOnError(): void
    {
        $this->testSkipOnErrorInternal(new InEnum(EnumStatus::class), new InEnum(EnumStatus::class, skipOnError: true));
    }

    public function testWhen(): void
    {
        $when = static fn (mixed $value): bool => $value !== null;
        $this->testWhenInternal(new InEnum(EnumStatus::class), new InEnum(EnumStatus::class, when: $when));
    }

    protected function getDifferentRuleInHandlerItems(): array
    {
        return [InEnum::class, InEnumHandler::class];
    }
}
