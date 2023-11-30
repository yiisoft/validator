<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use ArrayObject;
use Yiisoft\Validator\Rule\In;
use Yiisoft\Validator\Rule\InHandler;
use Yiisoft\Validator\Tests\Rule\Base\DifferentRuleInHandlerTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\RuleTestCase;
use Yiisoft\Validator\Tests\Rule\Base\RuleWithOptionsTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\SkipOnErrorTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\WhenTestTrait;

final class InTest extends RuleTestCase
{
    use DifferentRuleInHandlerTestTrait;
    use RuleWithOptionsTestTrait;
    use SkipOnErrorTestTrait;
    use WhenTestTrait;

    public function testGetName(): void
    {
        $rule = new In(range(1, 10));
        $this->assertSame(In::class, $rule->getName());
    }

    public function dataOptions(): array
    {
        return [
            [
                new In(range(1, 10)),
                [
                    'values' => range(1, 10),
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
                new In(range(1, 2), strict: true),
                [
                    'values' => [1, 2],
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
                new In(range(1, 2), not: true),
                [
                    'values' => [1, 2],
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
            'arrays, non-strict equality (fully), non-strict mode' => [['1', '2'], [new In([[1, 2], [3, 4]])]],
            'arrays, nested' => [
                ['data' => ['value' => [1, 2]]],
                [
                    new In([
                        ['data' => ['value' => [1, 2]]],
                        ['data' => ['value' => [3, 4]]],
                    ]),
                ],
            ],
            'arrays, nested, different keys order' => [
                [
                    'data2' => ['value2' => [7, 8], 'value1' => [5, 6]],
                    'data1' => ['value2' => [3, 4], 'value1' => [1, 2]],
                ],
                [
                    new In([
                        [
                            'data1' => ['value1' => [1, 2], 'value2' => [3, 4]],
                            'data2' => ['value1' => [5, 6], 'value2' => [7, 8]],
                        ],
                        [
                            'data1' => ['value1' => [9, 10], 'value2' => [11, 12]],
                            'data2' => ['value1' => [13, 14], 'value2' => [15, 16]],
                        ],
                    ]),
                ],
            ],
        ];
    }

    public function dataValidationFailed(): array
    {
        $errors = ['' => ['This value is not in the list of acceptable values.']];

        return [
            [0, [new In(range(1, 10))], $errors],
            [11, [new In(range(1, 10))], $errors],
            [5.5, [new In(range(1, 10))], $errors],

            [null, [new In(range(1, 10))], $errors],
            ['0', [new In(range(1, 10))], $errors],
            [0, [new In(range(1, 10))], $errors],
            ['', [new In(range(1, 10))], $errors],

            ['1', [new In(range(1, 10), strict: true)], $errors],
            ['10', [new In(range(1, 10), strict: true)], $errors],
            ['5.5', [new In(range(1, 10), strict: true)], $errors],
            [['1', '2', '3', '4', '5', '6'], [new In(range(1, 10), strict: true)], $errors],
            [['1', '2', '3', 4, 5, 6], [new In(range(1, 10), strict: true)], $errors],

            [1, [new In(range(1, 10), not: true)], $errors],
            [10, [new In(range(1, 10), not: true)], $errors],
            ['10', [new In(range(1, 10), not: true)], $errors],
            ['5', [new In(range(1, 10), not: true)], $errors],

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
            'arrays, items are not from acceptable list' => [[5, 6], [new In([[1, 2], [3, 4]])], $errors],
            'arrays, items from acceptable list but not as a unit' => [[2, 3], [new In([[1, 2], [3, 4]])], $errors],
            'arrays, reversed order' => [[2, 1], [new In([[1, 2], [3, 4]])], $errors],
            'arrays, nested, items are not from acceptable list' => [
                ['data' => ['value' => [5, 6]]],
                [
                    new In([
                        ['data' => ['value' => [1, 2]]],
                        ['data' => ['value' => [3, 4]]],
                    ]),
                ],
                $errors,
            ],
            'arrays, nested, reversed order of values in lists' => [
                [
                    'data2' => ['value2' => [8, 7], 'value1' => [6, 5]],
                    'data1' => ['value2' => [4, 3], 'value1' => [2, 1]],
                ],
                [
                    new In([
                        [
                            'data1' => ['value1' => [1, 2], 'value2' => [3, 4]],
                            'data2' => ['value1' => [5, 6], 'value2' => [7, 8]],
                        ],
                        [
                            'data1' => ['value1' => [9, 10], 'value2' => [11, 12]],
                            'data2' => ['value1' => [13, 14], 'value2' => [15, 16]],
                        ],
                    ]),
                ],
                $errors,
            ],
            'attempt to use a a subset' => [
                [
                    ['data' => ['value' => [1, 2]]],
                    ['data' => ['value' => [3, 4]]],
                ],
                [
                    new In([
                        ['data' => ['value' => [1, 2]]],
                        ['data' => ['value' => [3, 4]]],
                        ['data' => ['value' => [5, 6]]],
                    ]),
                ],
                $errors,
            ],

            'custom error' => [15, [new In(range(1, 10), message: 'Custom error')], ['' => ['Custom error']]],
        ];
    }

    public function testSkipOnError(): void
    {
        $this->testSkipOnErrorInternal(new In(range(1, 10)), new In(range(1, 10), skipOnError: true));
    }

    public function testWhen(): void
    {
        $when = static fn (mixed $value): bool => $value !== null;
        $this->testWhenInternal(new In(range(1, 10)), new In(range(1, 10), when: $when));
    }

    protected function getDifferentRuleInHandlerItems(): array
    {
        return [In::class, InHandler::class];
    }
}
