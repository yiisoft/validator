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
                        'message' => 'This value must contain at least {min, number} {min, plural, one{item} ' .
                            'other{items}}.',
                        'parameters' => ['min' => 3],
                    ],
                    'greaterThanMaxMessage' => [
                        'message' => 'This value must contain at most {max, number} {max, plural, one{item} ' .
                            'other{items}}.',
                        'parameters' => ['max' => null],
                    ],
                    'notExactlyMessage' => [
                        'message' => 'This value must contain exactly {exactly, number} {exactly, plural, one{item} ' .
                            'other{items}}.',
                        'parameters' => ['exactly' => null],
                    ],
                    'message' => [
                        'message' => 'This value must be an array or implement \Countable interface.',
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
            [1, [new Count(min: 3)], ['' => ['This value must be an array or implement \Countable interface.']]],
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

            [[0, 0, 0, 0], [new Count(max: 3, greaterThanMaxMessage: 'Custom message.')], ['' => ['Custom message.']]],
            [[0, 0, 0, 0], [new Count(exactly: 3, notExactlyMessage: 'Custom message.')], ['' => ['Custom message.']]],
            [[0, 0], [new Count(min: 3, lessThanMinMessage: 'Custom message.')], ['' => ['Custom message.']]],
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
