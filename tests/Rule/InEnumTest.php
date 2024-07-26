<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use ArrayObject;
use Yiisoft\Validator\Rule\In;
use Yiisoft\Validator\Rule\InEnum;
use Yiisoft\Validator\Rule\InEnumHandler;
use Yiisoft\Validator\Rule\InHandler;
use Yiisoft\Validator\Tests\Rule\Base\DifferentRuleInHandlerTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\RuleTestCase;
use Yiisoft\Validator\Tests\Rule\Base\RuleWithOptionsTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\SkipOnErrorTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\WhenTestTrait;
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
                        'template' => 'This value is not in the list of acceptable values.',
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
                        'template' => 'This value is not in the list of acceptable values.',
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
                        'template' => 'This value is not in the list of acceptable values.',
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
            [1, [new In(range(1, 10))]],
            [10, [new In(range(1, 10))]],
            ['10', [new In(range(1, 10))]],
            ['5', [new In(range(1, 10))]],

            [['a'], [new In([['a'], ['b']])]],
            ['a', [new In(new ArrayObject(['a', 'b']))]],

            [1, [new In(range(1, 10), strict: true)]],
            [5, [new In(range(1, 10), strict: true)]],
            [10, [new In(range(1, 10), strict: true)]],

            [0, [new In(range(1, 10), not: true)]],
            [11, [new In(range(1, 10), not: true)]],
            [5.5, [new In(range(1, 10), not: true)]],

            'arrays, simple case' => [[1, 2], [new In([[1, 2], [3, 4]])]],
            'arrays, non-strict equality (partially), non-strict mode' => [['1', 2], [new In([[1, 2], [3, 4]])]],
        ];
    }

    public function dataValidationFailed(): array
    {
        $errors = ['' => ['This value is not in the list of acceptable values.']];

        return [


            'arrays, non-strict equality (partially), strict mode' => [
                ['1', 2],
                [new In([[1, 2], [3, 4]], strict: true)],
                $errors,
            ],
            'arrays, non-strict equality (fully), strict mode' => [
                ['1', '2'],
                [new In([[1, 2], [3, 4]], strict: true)],
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
