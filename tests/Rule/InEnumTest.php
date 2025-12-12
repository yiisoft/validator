<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use InvalidArgumentException;
use Yiisoft\Validator\Rule\InEnum;
use Yiisoft\Validator\Rule\InEnumHandler;
use Yiisoft\Validator\Tests\Rule\Base\DifferentRuleInHandlerTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\RuleTestCase;
use Yiisoft\Validator\Tests\Rule\Base\RuleWithOptionsTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\SkipOnErrorTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\WhenTestTrait;
use Yiisoft\Validator\Tests\Support\Data\Enum\BackedEnumStatus;
use Yiisoft\Validator\Tests\Support\Data\Enum\EnumStatus;
use Yiisoft\Validator\Tests\Support\Data\Enum\IntBackedEnumStatus;
use Yiisoft\Validator\ValidationContext;

final class InEnumTest extends RuleTestCase
{
    use DifferentRuleInHandlerTestTrait;
    use RuleWithOptionsTestTrait;
    use SkipOnErrorTestTrait;
    use WhenTestTrait;

    public function testInvalidEnum(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new InEnum('test');
    }

    public function testGetName(): void
    {
        $rule = new InEnum(EnumStatus::class);
        $this->assertSame('inEnum', $rule->getName());
    }

    public static function dataOptions(): array
    {
        $values = array_column(EnumStatus::class::cases(), 'name');

        return [
            'non-strict' => [
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
            'strict' => [
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
            'not' => [
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

    public static function dataValidationPassed(): array
    {
        return [
            ['DRAFT', [new InEnum(EnumStatus::class)]],
            ['PUBLISHED', [new InEnum(EnumStatus::class)]],

            ['DRAFT', [new InEnum(BackedEnumStatus::class, useNames: true)]],
            ['PUBLISHED', [new InEnum(BackedEnumStatus::class, useNames: true)]],


            ['draft', [new InEnum(BackedEnumStatus::class)]],
            ['published', [new InEnum(BackedEnumStatus::class)]],

            [1, [new InEnum(IntBackedEnumStatus::class)]],
            [2, [new InEnum(IntBackedEnumStatus::class)]],
            ['1', [new InEnum(IntBackedEnumStatus::class)]],
            ['2', [new InEnum(IntBackedEnumStatus::class)]],
        ];
    }

    public static function dataValidationFailed(): array
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

            [
                '1',
                [new InEnum(IntBackedEnumStatus::class, strict: true)],
                $errors,
            ],
        ];
    }

    public function testValidationMessageContainsNecessaryParameters(): void
    {
        $rule = (new InEnum(EnumStatus::class));

        $result = (new InEnumHandler())->validate('aaa', $rule, new ValidationContext());
        foreach ($result->getErrors() as $error) {
            $parameters = $error->getParameters();
            $this->assertArrayHasKey('property', $parameters);
            $this->assertArrayHasKey('Property', $parameters);
        }
    }

    public function testSkipOnError(): void
    {
        $this->testSkipOnErrorInternal(new InEnum(EnumStatus::class), new InEnum(EnumStatus::class, skipOnError: true));
    }

    public function testWhen(): void
    {
        $when = static fn(mixed $value): bool => $value !== null;
        $this->testWhenInternal(new InEnum(EnumStatus::class), new InEnum(EnumStatus::class, when: $when));
    }

    protected function getDifferentRuleInHandlerItems(): array
    {
        return [InEnum::class, InEnumHandler::class];
    }
}
