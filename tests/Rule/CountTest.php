<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use Countable;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use stdClass;
use Yiisoft\Validator\DataSet\MixedDataSet;
use Yiisoft\Validator\Rule\Count;
use Yiisoft\Validator\Rule\CountHandler;
use Yiisoft\Validator\Tests\Rule\Base\DifferentRuleInHandlerTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\RuleTestCase;
use Yiisoft\Validator\Tests\Rule\Base\SerializableRuleTestTrait;
use Yiisoft\Validator\Tests\Support\Rule\RuleWithCustomHandler;
use Yiisoft\Validator\Tests\Support\ValidatorFactory;

final class CountTest extends RuleTestCase
{
    use SerializableRuleTestTrait;
    use DifferentRuleInHandlerTestTrait;

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
                new MixedDataSet(
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

    public function dataInitWithMinAndMaxAndExactly(): array
    {
        return [
            [['min' => 3, 'exactly' => 3]],
            [['max' => 3, 'exactly' => 3]],
            [['min' => 3, 'max' => 3, 'exactly' => 3]],
        ];
    }

    /**
     * @dataProvider dataInitWithMinAndMaxAndExactly
     */
    public function testInitWithMinAndMaxAndExactly(array $arguments): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('$exactly is mutually exclusive with $min and $max.');
        new Count(...$arguments);
    }

    public function testInitWithMinAndMax(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Use $exactly instead.');
        new Count(min: 3, max: 3);
    }

    public function testInitWithoutRequiredArguments(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('At least one of these attributes must be specified: $min, $max, $exactly.');
        new Count();
    }

    protected function getDifferentRuleInHandlerItems(): array
    {
        return [Count::class, CountHandler::class];
    }
}
