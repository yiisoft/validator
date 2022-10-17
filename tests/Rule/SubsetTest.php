<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use ArrayObject;
use Yiisoft\Validator\DataSet\MixedDataSet;
use Yiisoft\Validator\Rule\Subset;
use Yiisoft\Validator\Rule\SubsetHandler;
use Yiisoft\Validator\Tests\Rule\Base\DifferentRuleInHandlerTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\RuleTestCase;
use Yiisoft\Validator\Tests\Rule\Base\SerializableRuleTestTrait;

final class SubsetTest extends RuleTestCase
{
    use DifferentRuleInHandlerTestTrait;
    use SerializableRuleTestTrait;

    public function testGetName(): void
    {
        $rule = new Subset([]);
        $this->assertSame('subset', $rule->getName());
    }

    public function dataOptions(): array
    {
        return [
            [
                new Subset([]),
                [
                    'values' => [],
                    'strict' => false,
                    'iterableMessage' => [
                        'message' => 'Value must be iterable.',
                    ],
                    'subsetMessage' => [
                        'message' => 'Values must be ones of {values}.',
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
            [new MixedDataSet(new ArrayObject(['a', 'b'])), [new Subset(['a', 'b', 'c'])]],
        ];
    }

    public function dataValidationFailed(): array
    {
        return [
            [
                [0, 1, 2],
                [new Subset(range(1, 10))],
                ['' => ['Values must be ones of "1", "2", "3", "4", "5", "6", "7", "8", "9", "10".']],
            ],
            [
                [10, 11, 12],
                [new Subset(range(1, 10))],
                ['' => ['Values must be ones of "1", "2", "3", "4", "5", "6", "7", "8", "9", "10".']],
            ],
            'custom error' => [
                ['data' => ['2']],
                ['data' => new Subset(['a'], subsetMessage: 'Custom error')],
                ['data' => ['Custom error']],
            ],
        ];
    }

    protected function getDifferentRuleInHandlerItems(): array
    {
        return [Subset::class, SubsetHandler::class];
    }
}
