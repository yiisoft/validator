<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use ArrayIterator;
use Countable;
use stdClass;
use Yiisoft\Validator\DataSet\SingleValueDataSet;
use Yiisoft\Validator\Rule\Count;
use Yiisoft\Validator\Rule\CountHandler;
use Yiisoft\Validator\Tests\Rule\Base\CountableLimitTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\DifferentRuleInHandlerTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\RuleTestCase;
use Yiisoft\Validator\Tests\Rule\Base\RuleWithOptionsTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\SkipOnErrorTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\WhenTestTrait;
use Yiisoft\Validator\Tests\Support\Data\CountDto;

final class CountTest extends RuleTestCase
{
    use CountableLimitTestTrait;
    use DifferentRuleInHandlerTestTrait;
    use RuleWithOptionsTestTrait;
    use SkipOnErrorTestTrait;
    use WhenTestTrait;

    public function testGetName(): void
    {
        $rule = new Count(min: 3);
        $this->assertSame(Count::class, $rule->getName());
    }

    public function dataOptions(): array
    {
        return [
            [
                new Count(min: 3),
                [
                    'min' => 3,
                    'max' => null,
                    'exactly' => null,
                    'lessThanMinMessage' => [
                        'template' => '{Property} must contain at least {min, number} {min, plural, one{item} ' .
                            'other{items}}.',
                        'parameters' => ['min' => 3],
                    ],
                    'greaterThanMaxMessage' => [
                        'template' => '{Property} must contain at most {max, number} {max, plural, one{item} ' .
                            'other{items}}.',
                        'parameters' => ['max' => null],
                    ],
                    'notExactlyMessage' => [
                        'template' => '{Property} must contain exactly {exactly, number} {exactly, plural, one{item} ' .
                            'other{items}}.',
                        'parameters' => ['exactly' => null],
                    ],
                    'incorrectInputMessage' => [
                        'template' => '{Property} must be an array or implement \Countable interface.',
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
            [[0, 0, 0], [new Count(min: 3)]],
            [[0, 0, 0, 0], [new Count(min: 3)]],
            [[0, 0, 0], [new Count(3)]],
            [[], [new Count(max: 3)]],
            [[0, 0], [new Count(max: 3)]],
            [[0, 0, 0], [new Count(max: 3)]],
            'value: array iterator with min allowed count, min: positive' => [
                new ArrayIterator([0, 0, 0]),
                [new Count(min: 3)],
            ],
            [
                new SingleValueDataSet(
                    new class () implements Countable {
                        protected int $myCount = 3;

                        public function count(): int
                        {
                            return $this->myCount;
                        }
                    }
                ),
                [new Count(min: 3)],
            ],
            'class attribute' => [
                new CountDto(7),
            ],
            'value: empty array, exactly: 0' => [
                [],
                [new Count(0)],
            ],
            'value: empty array, min: 0' => [
                [],
                [new Count(min: 0)],
            ],
            'value: empty array, max: 0' => [
                [],
                [new Count(max: 0)],
            ],
            'value: empty array iterator, exactly: 0' => [
                new ArrayIterator(),
                [new Count(0)],
            ],
            'value: empty array iterator, exactly: positive, skipOnEmpty: true' => [
                new ArrayIterator(),
                [new Count(1, skipOnEmpty: true)],
            ],
        ];
    }

    public function dataValidationFailed(): array
    {
        $lessThanMinmessage = 'Value must contain at least 3 items.';
        $greaterThanMaxMessage = 'Value must contain at most 3 items.';

        return [
            'incorrect input' => [
                1,
                [new Count(min: 3)],
                ['' => ['Value must be an array or implement \Countable interface.']],
            ],
            'custom incorrect input message' => [
                1,
                [new Count(min: 3, incorrectInputMessage: 'Custom incorrect input message.')],
                ['' => ['Custom incorrect input message.']],
            ],
            'custom incorrect input message with parameters' => [
                1,
                [new Count(min: 3, incorrectInputMessage: 'Attribute - {property}, type - {type}.')],
                ['' => ['Attribute - value, type - int.']],
            ],
            'custom incorrect input message, attribute set' => [
                ['data' => 1],
                ['data' => new Count(min: 3, incorrectInputMessage: 'Attribute - {property}, type - {type}.')],
                ['data' => ['Attribute - data, type - int.']],
            ],

            [[1], new Count(3), ['' => ['Value must contain exactly 3 items.']]],
            [[1], [new Count(min: 3)], ['' => [$lessThanMinmessage]]],
            [[], [new Count(min: 3)], ['' => [$lessThanMinmessage]]],
            [[0, 0], [new Count(min: 3)], ['' => [$lessThanMinmessage]]],
            [[1.1], [new Count(min: 3)], ['' => [$lessThanMinmessage]]],
            [[''], [new Count(min: 3)], ['' => [$lessThanMinmessage]]],
            [['some string'], [new Count(min: 3)], ['' => [$lessThanMinmessage]]],
            [[new stdClass()], [new Count(min: 3)], ['' => [$lessThanMinmessage]]],
            'value: array iterator with lower count, min: positive' => [
                new ArrayIterator([0, 0]),
                [new Count(min: 3)],
                ['' => [$lessThanMinmessage]],
            ],
            // https://www.php.net/manual/ru/class.countable.php
            'value: class with min count returned from count method but not implenting Countable interface, min: 3' => [
                [
                    new class () {
                        protected int $myCount = 3;

                        public function count(): int
                        {
                            return $this->myCount;
                        }
                    },
                ],
                [new Count(min: 3)],
                ['' => [$lessThanMinmessage]],
            ],
            [[0, 0, 0, 0], [new Count(max: 3)], ['' => [$greaterThanMaxMessage]]],

            'custom less than min message' => [
                [0, 0],
                [new Count(min: 3, lessThanMinMessage: 'Custom less than min message.')],
                ['' => ['Custom less than min message.']],
            ],
            'custom less than min message with parameters' => [
                [0, 0],
                [new Count(min: 3, lessThanMinMessage: 'Min - {min}, attribute - {property}, number - {number}.')],
                ['' => ['Min - 3, attribute - value, number - 2.']],
            ],
            'custom less than min message with parameters, attribute set' => [
                ['data' => [0, 0]],
                [
                    'data' => new Count(
                        min: 3,
                        lessThanMinMessage: 'Min - {min}, attribute - {property}, number - {number}.',
                    ),
                ],
                ['data' => ['Min - 3, attribute - data, number - 2.']],
            ],

            'custom greater than max message' => [
                [0, 0, 0, 0],
                [new Count(max: 3, greaterThanMaxMessage: 'Custom greater than max message.')],
                ['' => ['Custom greater than max message.']],
            ],
            'custom greater than max message with parameters' => [
                [0, 0, 0, 0],
                [new Count(max: 3, greaterThanMaxMessage: 'Max - {max}, attribute - {property}, number - {number}.')],
                ['' => ['Max - 3, attribute - value, number - 4.']],
            ],
            'custom greater than max message with parameters, attribute set' => [
                ['data' => [0, 0, 0, 0]],
                [
                    'data' => new Count(
                        max: 3,
                        greaterThanMaxMessage: 'Max - {max}, attribute - {property}, number - {number}.',
                    ),
                ],
                ['data' => ['Max - 3, attribute - data, number - 4.']],
            ],

            'custom not exactly message' => [
                [0, 0, 0, 0],
                [new Count(3, notExactlyMessage: 'Custom not exactly message.')],
                ['' => ['Custom not exactly message.']],
            ],
            'custom not exactly message with parameters' => [
                [0, 0, 0, 0],
                [
                    new Count(
                        exactly: 3,
                        notExactlyMessage: 'Exactly - {exactly}, attribute - {property}, number - {number}.',
                    ),
                ],
                ['' => ['Exactly - 3, attribute - value, number - 4.']],
            ],
            'custom not exactly message with parameters, attribute set' => [
                ['data' => [0, 0, 0, 0]],
                [
                    'data' => new Count(
                        exactly: 3,
                        notExactlyMessage: 'Exactly - {exactly}, attribute - {property}, number - {number}.',
                    ),
                ],
                ['data' => ['Exactly - 3, attribute - data, number - 4.']],
            ],
            'class attribute' => [
                new CountDto(),
                null,
                ['' => ['Value must contain at least 2 items.']],
            ],
            'value: array with greater count, exactly: 0' => [
                [0],
                [new Count(0)],
                ['' => ['Value must contain exactly 0 items.']],
            ],
            'value: empty array iterator, exactly: positive' => [
                new ArrayIterator(),
                [new Count(1)],
                ['' => ['Value must contain exactly 1 item.']],
            ],
        ];
    }

    public function testSkipOnError(): void
    {
        $this->testSkipOnErrorInternal(new Count(min: 3), new Count(min: 3, skipOnError: true));
    }

    public function testWhen(): void
    {
        $when = static fn (mixed $value): bool => $value !== null;
        $this->testWhenInternal(new Count(min: 3), new Count(min: 3, when: $when));
    }

    protected function getRuleClass(): string
    {
        return Count::class;
    }

    protected function getDifferentRuleInHandlerItems(): array
    {
        return [Count::class, CountHandler::class];
    }
}
