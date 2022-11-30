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
                        'template' => 'Value must be iterable.',
                        'parameters' => [],
                    ],
                    'subsetMessage' => [
                        'template' => 'Values must be ones of {values}.',
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
        ];
    }

    public function dataValidationFailed(): array
    {
        return [
            'non-iterable' => [
                1,
                [new Subset([1, 2, 3])],
                ['' => ['Value must be iterable.']],
            ],
            'custom non-iterable message' => [
                1,
                [new Subset([1, 2, 3], iterableMessage: 'Custom non-iterable message.')],
                ['' => ['Custom non-iterable message.']],
            ],
            'custom non-iterable message with parameters' => [
                1,
                [new Subset([1, 2, 3], iterableMessage: 'Attribute - {attribute}, type - {type}.')],
                ['' => ['Attribute - , type - int.']],
            ],
            'custom non-iterable message with parameters, attribute set' => [
                ['data' => 1],
                ['data' => new Subset([1, 2, 3], iterableMessage: 'Attribute - {attribute}, type - {type}.')],
                ['data' => ['Attribute - data, type - int.']],
            ],
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
            'iterator as a value' => [
                new SingleValueDataSet(new ArrayObject(['c', 'd'])),
                [new Subset(new ArrayObject(['a', 'b', 'c']))],
                ['' => ['Values must be ones of "a", "b", "c".']],
            ],
            'custom message' => [
                ['' => ['c']],
                ['' => new Subset(['a', 'b'], subsetMessage: 'Custom message.')],
                ['' => ['Custom message.']],
            ],
            'custom subset message with parameters' => [
                ['' => ['c']],
                ['' => new Subset(['a', 'b'], subsetMessage: 'Attribute - {attribute}, values - {values}.')],
                ['' => ['Attribute - , values - "a", "b".']],
            ],
            'custom subset message with parameters, attribute set' => [
                ['data' => ['c']],
                ['data' => new Subset(['a', 'b'], subsetMessage: 'Attribute - {attribute}, values - {values}.')],
                ['data' => ['Attribute - data, values - "a", "b".']],
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
