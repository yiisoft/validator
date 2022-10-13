<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use ArrayObject;
use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\Rule\InRange;
use Yiisoft\Validator\Rule\InRangeHandler;
use Yiisoft\Validator\Tests\Rule\Base\DifferentRuleInHandlerTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\RuleTestCase;
use Yiisoft\Validator\Tests\Rule\Base\SerializableRuleTestTrait;
use Yiisoft\Validator\Tests\Support\ValidatorFactory;
use Yiisoft\Validator\Tests\Support\Rule\RuleWithCustomHandler;

final class InRangeTest extends RuleTestCase
{
    use SerializableRuleTestTrait;
    use DifferentRuleInHandlerTestTrait;

    public function testGetName(): void
    {
        $rule = new InRange(range(1, 10));
        $this->assertSame('inRange', $rule->getName());
    }

    public function dataOptions(): array
    {
        return [
            [
                new InRange(range(1, 10)),
                [
                    'range' => range(1, 10),
                    'strict' => false,
                    'not' => false,
                    'message' => [
                        'message' => 'This value is invalid.',
                    ],
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                new InRange(range(1, 2), strict: true),
                [
                    'range' => [1, 2],
                    'strict' => true,
                    'not' => false,
                    'message' => [
                        'message' => 'This value is invalid.',
                    ],
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                new InRange(range(1, 2), not: true),
                [
                    'range' => [1, 2],
                    'strict' => false,
                    'not' => true,
                    'message' => [
                        'message' => 'This value is invalid.',
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
            [1, [new InRange(range(1, 10))]],
            [10, [new InRange(range(1, 10))]],
            ['10', [new InRange(range(1, 10))]],
            ['5', [new InRange(range(1, 10))]],

            [['a'], [new InRange([['a'], ['b']])]],
            ['a', [new InRange(new ArrayObject(['a', 'b']))]],

            [1, [new InRange(range(1, 10), strict: true)]],
            [5, [new InRange(range(1, 10), strict: true)]],
            [10, [new InRange(range(1, 10), strict: true)]],

            [0, [new InRange(range(1, 10), not: true)]],
            [11, [new InRange(range(1, 10), not: true)]],
            [5.5, [new InRange(range(1, 10), not: true)]],
        ];
    }

    public function dataValidationFailed(): array
    {
        $errors = ['' => ['This value is invalid.']];

        return [
            [0, [new InRange(range(1, 10))], $errors],
            [11, [new InRange(range(1, 10))], $errors],
            [5.5, [new InRange(range(1, 10))], $errors],

            [null, [new InRange(range(1, 10))], $errors],
            ['0', [new InRange(range(1, 10))], $errors],
            [0, [new InRange(range(1, 10))], $errors],
            ['', [new InRange(range(1, 10))], $errors],

            ['1', [new InRange(range(1, 10), strict: true)], $errors],
            ['10', [new InRange(range(1, 10), strict: true)], $errors],
            ['5.5', [new InRange(range(1, 10), strict: true)], $errors],
            [['1', '2', '3', '4', '5', '6'], [new InRange(range(1, 10), strict: true)], $errors],
            [['1', '2', '3', 4, 5, 6], [new InRange(range(1, 10), strict: true)], $errors],

            [1, [new InRange(range(1, 10), not: true)], $errors],
            [10, [new InRange(range(1, 10), not: true)], $errors],
            ['10', [new InRange(range(1, 10), not: true)], $errors],
            ['5', [new InRange(range(1, 10), not: true)], $errors],

            'custom error' => [15, [new InRange(range(1, 10), message: 'Custom error')], ['' => ['Custom error']]],
        ];
    }

    protected function getDifferentRuleInHandlerItems(): array
    {
        return [InRange::class, InRangeHandler::class];
    }
}
