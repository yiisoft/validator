<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use ArrayObject;
use Yiisoft\Validator\DataSet\SingleValueDataSet;
use Yiisoft\Validator\Rule\Subset;
use Yiisoft\Validator\Rule\SubsetHandler;
use Yiisoft\Validator\Tests\Rule\Base\DifferentRuleInHandlerTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\RuleTestCase;
use Yiisoft\Validator\Tests\Rule\Base\RuleWithOptionsTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\SkipOnErrorTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\WhenTestTrait;

final class SubsetTest extends RuleTestCase
{
    use DifferentRuleInHandlerTestTrait;
    use RuleWithOptionsTestTrait;
    use SkipOnErrorTestTrait;
    use WhenTestTrait;

    public function testGetName(): void
    {
        $rule = new Subset([]);
        $this->assertSame(Subset::class, $rule->getName());
    }

    public function dataOptions(): array
    {
        return [
            [
                new Subset([]),
                [
                    'values' => [],
                    'strict' => false,
                    'incorrectInputMessage' => [
                        'template' => '{Attribute} must be iterable.',
                        'parameters' => [],
                    ],
                    'message' => [
                        'template' => '{Attribute} is not a subset of acceptable values.',
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
            [[], [new Subset(range(1, 10))]],
            [[1, 2, 3, 4, 5], [new Subset(range(1, 10))]],
            [[6, 7, 8, 9, 10], [new Subset(range(1, 10))]],
            [['1', '2', '3', 4, 5, 6], [new Subset(range(1, 10))]],

            [['a', 'b'], [new Subset(['a', 'b', 'c'])]],
            [new SingleValueDataSet(new ArrayObject(['a', 'b'])), [new Subset(new ArrayObject(['a', 'b', 'c']))]],

            'arrays, simple case' => [[[1, 2], [3, 4]], [new Subset([[1, 2], [3, 4], [5, 6]])]],
            'arrays, reversed order' => [[[3, 4], [1, 2]], [new Subset([[1, 2], [3, 4], [5, 6]])]],
            'arrays, non-strict equality (partially), non-strict mode' => [
                [['1', 2], ['3', 4]],
                [new Subset([[1, 2], [3, 4], [5, 6]])],
            ],
            'arrays, non-strict equality (fully), non-strict mode' => [
                [['1', '2'], ['3', '4']],
                [new Subset([[1, 2], [3, 4], [5, 6]])],
            ],
            'arrays, nested' => [
                [
                    ['data' => ['value' => [1, 2]]],
                    ['data' => ['value' => [3, 4]]],
                ],
                [
                    new Subset([
                        ['data' => ['value' => [1, 2]]],
                        ['data' => ['value' => [3, 4]]],
                        ['data' => ['value' => [5, 6]]],
                    ]),
                ],
            ],
            'arrays, nested, different keys order' => [
                [
                    [
                        'data2' => ['value2' => [7, 8], 'value1' => [5, 6]],
                        'data1' => ['value2' => [3, 4], 'value1' => [1, 2]],
                    ],
                    [
                        'data2' => ['value2' => [15, 16], 'value1' => [13, 14]],
                        'data1' => ['value2' => [11, 12], 'value1' => [9, 10]],
                    ],
                ],
                [
                    new Subset([
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
        $errors = ['' => ['Value is not a subset of acceptable values.']];

        return [
            'non-iterable' => [
                1,
                [new Subset([1, 2, 3])],
                ['' => ['Value must be iterable.']],
            ],
            'custom incorrect input message' => [
                1,
                [new Subset([1, 2, 3], incorrectInputMessage: 'Custom non-iterable message.')],
                ['' => ['Custom non-iterable message.']],
            ],
            'custom incorrect input message with parameters' => [
                1,
                [new Subset([1, 2, 3], incorrectInputMessage: 'Attribute - {attribute}, type - {type}.')],
                ['' => ['Attribute - value, type - int.']],
            ],
            'custom incorrect input message with parameters, attribute set' => [
                ['data' => 1],
                ['data' => new Subset([1, 2, 3], incorrectInputMessage: 'Attribute - {attribute}, type - {type}.')],
                ['data' => ['Attribute - data, type - int.']],
            ],
            [
                [0, 1, 2],
                [new Subset(range(1, 10))],
                $errors,
            ],
            [
                [10, 11, 12],
                [new Subset(range(1, 10))],
                $errors,
            ],
            'iterator as a value' => [
                new SingleValueDataSet(new ArrayObject(['c', 'd'])),
                [new Subset(new ArrayObject(['a', 'b', 'c']))],
                $errors,
            ],

            'arrays, non-strict equality (partially), strict mode' => [
                [['1', 2], ['3', 4]],
                [new Subset([[1, 2], [3, 4], [5, 6]], strict: true)],
                $errors,
            ],
            'arrays, non-strict equality (fully), strict mode' => [
                [['1', '2'], ['3', '4']],
                [new Subset([[1, 2], [3, 4], [5, 6]], strict: true)],
                $errors,
            ],
            'arrays, items are not from acceptable list' => [
                [[7, 8], [9, 10]],
                [new Subset([[1, 2], [3, 4], [5, 6]], strict: true)],
                $errors,
            ],
            'arrays, items from acceptable list but not as units' => [
                [[2, 3], [4, 5]],
                [new Subset([[1, 2], [3, 4], [5, 6]], strict: true)],
                $errors,
            ],
            'arrays, nested, not all items are from acceptable list' => [
                [
                    ['data' => ['value' => [3, 4]]],
                    ['data' => ['value' => [5, 7]]],
                ],
                [
                    new Subset([
                        ['data' => ['value' => [1, 2]]],
                        ['data' => ['value' => [3, 4]]],
                        ['data' => ['value' => [5, 6]]],
                    ]),
                ],
                $errors,
            ],
            'arrays, nested, reversed order of values in lists' => [
                [
                    [
                        'data2' => ['value2' => [8, 7], 'value1' => [6, 5]],
                        'data1' => ['value2' => [4, 3], 'value1' => [2, 1]],
                    ],
                    [
                        'data2' => ['value2' => [16, 15], 'value1' => [14, 13]],
                        'data1' => ['value2' => [12, 11], 'value1' => [10, 9]],
                    ],
                ],
                [
                    new Subset([
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

            'custom message' => [
                ['' => ['c']],
                ['' => new Subset(['a', 'b'], message: 'Custom message.')],
                ['' => ['Custom message.']],
            ],
            'custom message with parameters' => [
                ['' => ['c']],
                ['' => new Subset(['a', 'b'], message: 'Attribute - {attribute}.')],
                ['' => ['Attribute - .']],
            ],
            'custom message with parameters, attribute set' => [
                ['data' => ['c']],
                ['data' => new Subset(['a', 'b'], message: 'Attribute - {attribute}.')],
                ['data' => ['Attribute - data.']],
            ],
        ];
    }

    public function testSkipOnError(): void
    {
        $this->testSkipOnErrorInternal(new Subset([]), new Subset([], skipOnError: true));
    }

    public function testWhen(): void
    {
        $when = static fn (mixed $value): bool => $value !== null;
        $this->testWhenInternal(new Subset([]), new Subset([], when: $when));
    }

    protected function getDifferentRuleInHandlerItems(): array
    {
        return [Subset::class, SubsetHandler::class];
    }
}
