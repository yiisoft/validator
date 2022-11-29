<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use Countable;
use stdClass;
use Yiisoft\Validator\DataSet\SingleValueDataSet;
use Yiisoft\Validator\Rule\Count;
use Yiisoft\Validator\Rule\CountHandler;
use Yiisoft\Validator\Tests\Rule\Base\DifferentRuleInHandlerTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\LimitTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\RuleTestCase;
use Yiisoft\Validator\Tests\Rule\Base\SerializableRuleTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\SkipOnErrorTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\WhenTestTrait;

final class CountTest extends RuleTestCase
{
    use DifferentRuleInHandlerTestTrait;
    use LimitTestTrait;
    use SerializableRuleTestTrait;
    use SkipOnErrorTestTrait;
    use WhenTestTrait;

    public function testGetName(): void
    {
        $rule = new Count(min: 3);
        $this->assertSame('count', $rule->getName());
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
                        'template' => 'This value must contain at least {min, number} {min, plural, one{item} ' .
                            'other{items}}.',
                        'parameters' => ['min' => 3],
                    ],
                    'greaterThanMaxMessage' => [
                        'template' => 'This value must contain at most {max, number} {max, plural, one{item} ' .
                            'other{items}}.',
                        'parameters' => ['max' => null],
                    ],
                    'notExactlyMessage' => [
                        'template' => 'This value must contain exactly {exactly, number} {exactly, plural, one{item} ' .
                            'other{items}}.',
                        'parameters' => ['exactly' => null],
                    ],
                    'incorrectInputMessage' => [
                        'template' => 'This value must be an array or implement \Countable interface.',
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
            [[0, 0, 0], [new Count(exactly: 3)]],
            [[], [new Count(max: 3)]],
            [[0, 0], [new Count(max: 3)]],
            [[0, 0, 0], [new Count(max: 3)]],
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
        ];
    }

    public function dataValidationFailed(): array
    {
        $lessThanMinmessage = 'This value must contain at least 3 items.';
        $greaterThanMaxMessage = 'This value must contain at most 3 items.';

        return [
            'incorrect input' => [
                1,
                [new Count(min: 3)],
                ['' => ['This value must be an array or implement \Countable interface.']],
            ],
            'custom incorrect input message' => [
                1,
                [new Count(min: 3, incorrectInputMessage: 'Custom incorrect input message.')],
                ['' => ['Custom incorrect input message.']],
            ],
            'custom incorrect input message with parameters' => [
                1,
                [new Count(min: 3, incorrectInputMessage: 'Attribute - {attribute}, type - {type}.')],
                ['' => ['Attribute - , type - int.']],
            ],
            'custom incorrect input message, attribute set' => [
                ['data' => 1],
                ['data' => new Count(min: 3, incorrectInputMessage: 'Attribute - {attribute}, type - {type}.')],
                ['data' => ['Attribute - data, type - int.']],
            ],

            [[1], [new Count(min: 3)], ['' => [$lessThanMinmessage]]],
            [[], [new Count(min: 3)], ['' => [$lessThanMinmessage]]],
            [[0, 0], [new Count(min: 3)], ['' => [$lessThanMinmessage]]],
            [[1.1], [new Count(min: 3)], ['' => [$lessThanMinmessage]]],
            [[''], [new Count(min: 3)], ['' => [$lessThanMinmessage]]],
            [['some string'], [new Count(min: 3)], ['' => [$lessThanMinmessage]]],
            [[new stdClass()], [new Count(min: 3)], ['' => [$lessThanMinmessage]]],
            // https://www.php.net/manual/ru/class.countable.php
            [
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
                [new Count(min: 3, lessThanMinMessage: 'Min - {min}, attribute - {attribute}, number - {number}.')],
                ['' => ['Min - 3, attribute - , number - 2.']],
            ],
            'custom less than min message with parameters, attribute set' => [
                ['data' => [0, 0]],
                [
                    'data' => new Count(
                        min: 3,
                        lessThanMinMessage: 'Min - {min}, attribute - {attribute}, number - {number}.',
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
                [new Count(max: 3, greaterThanMaxMessage: 'Max - {max}, attribute - {attribute}, number - {number}.')],
                ['' => ['Max - 3, attribute - , number - 4.']],
            ],
            'custom greater than max message with parameters, attribute set' => [
                ['data' => [0, 0, 0, 0]],
                [
                    'data' => new Count(
                        max: 3,
                        greaterThanMaxMessage: 'Max - {max}, attribute - {attribute}, number - {number}.',
                    ),
                ],
                ['data' => ['Max - 3, attribute - data, number - 4.']],
            ],

            'custom not exactly message' => [
                [0, 0, 0, 0],
                [new Count(exactly: 3, notExactlyMessage: 'Custom not exactly message.')],
                ['' => ['Custom not exactly message.']],
            ],
            'custom not exactly message with parameters' => [
                [0, 0, 0, 0],
                [
                    new Count(
                        exactly: 3,
                        notExactlyMessage: 'Exactly - {exactly}, attribute - {attribute}, number - {number}.',
                    ),
                ],
                ['' => ['Exactly - 3, attribute - , number - 4.']],
            ],
            'custom not exactly message with parameters, attribute set' => [
                ['data' => [0, 0, 0, 0]],
                [
                    'data' => new Count(
                        exactly: 3,
                        notExactlyMessage: 'Exactly - {exactly}, attribute - {attribute}, number - {number}.',
                    ),
                ],
                ['data' => ['Exactly - 3, attribute - data, number - 4.']],
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
