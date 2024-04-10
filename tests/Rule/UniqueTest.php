<?php

declare(strict_types=1);

namespace Rule;

use DateTime;
use stdClass;
use Stringable;
use Yiisoft\Validator\Rule\Unique;
use Yiisoft\Validator\Rule\UniqueHandler;
use Yiisoft\Validator\Tests\Rule\Base\DifferentRuleInHandlerTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\RuleTestCase;
use Yiisoft\Validator\Tests\Rule\Base\RuleWithOptionsTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\SkipOnErrorTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\WhenTestTrait;

final class UniqueTest extends RuleTestCase
{
    use DifferentRuleInHandlerTestTrait;
    use RuleWithOptionsTestTrait;
    use SkipOnErrorTestTrait;
    use WhenTestTrait;

    public function testGetName(): void
    {
        $rule = new Unique();
        $this->assertSame('unique', $rule->getName());
    }

    public function dataOptions(): array
    {
        return [
            'default' => [
                new Unique(),
                [
                    'incorrectInputMessage' => [
                        'template' => 'Value must be array or iterable.',
                        'parameters' => [],
                    ],
                    'incorrectItemValueMessage' => [
                        'template' => 'The allowed types for iterable\'s item values are integer, float, string, ' .
                            'boolean, null and object implementing \Stringable or \DateTimeInterface.',
                        'parameters' => [],
                    ],
                    'message' => [
                        'template' => 'Every iterable\'s item must be unique.',
                        'parameters' => [],
                    ],
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            'custom' => [
                new Unique(
                    incorrectInputMessage: 'Custom message 1.',
                    incorrectItemValueMessage: 'Custom message 2.',
                    message: 'Custom message 3.',
                    skipOnEmpty: true,
                    skipOnError: true
                ),
                [
                    'incorrectInputMessage' => [
                        'template' => 'Custom message 1.',
                        'parameters' => [],
                    ],
                    'incorrectItemValueMessage' => [
                        'template' => 'Custom message 2.',
                        'parameters' => [],
                    ],
                    'message' => [
                        'template' => 'Custom message 3.',
                        'parameters' => [],
                    ],
                    'skipOnEmpty' => true,
                    'skipOnError' => true,
                ],
            ],
        ];
    }

    public function dataValidationPassed(): array
    {
        return [
            'null' => [[null], new Unique()],
            'strings' => [['a', 'b'], new Unique()],
            'integers' => [[1, 2], new Unique()],
            'floats' => [[1.5, 2.5], new Unique()],
            'boolean values' => [[false, true], new Unique()],
            'stringable values' => [
                [
                    new class () implements Stringable {
                        public function __toString()
                        {
                            return 'a';
                        }
                    },
                    new class () implements Stringable {
                        public function __toString()
                        {
                            return 'b';
                        }
                    },
                ],
                new Unique(),
            ],
            'datetime values' => [
                [new DateTime('2024-04-10 14:05:01'), new DateTime('2024-04-10 14:05:02')],
                new Unique(),
            ],
            'mix, all allowed types' => [
                [
                    null,
                    'a',
                    'b',
                    1,
                    2,
                    1.5,
                    2.5,
                    false,
                    true,
                    new class () implements Stringable
                    {
                        public function __toString()
                        {
                            return 'c';
                        }
                    },
                    new class () implements Stringable
                    {
                        public function __toString()
                        {
                            return 'd';
                        }
                    },
                    new DateTime('2024-04-10 14:05:01'),
                    new DateTime('2024-04-10 14:05:02'),
                ],
                new Unique(),
            ],
            'mix, timestamps and datetime values' => [
                [
                    new DateTime('2024-04-10 14:05:01'),
                    new DateTime('2024-04-10 14:05:02'),
                    (new DateTime('2024-04-10 14:05:01'))->getTimestamp(),
                ],
                new Unique(),
            ],
        ];
    }

    public function dataValidationFailed(): array
    {
        $incorrectInputMessage = 'Value must be array or iterable.';
        $incorrectItemValueMessage = 'The allowed types for iterable\'s item values are integer, float, string, ' .
            'boolean, null and object implementing \Stringable or \DateTimeInterface.';
        $message = 'Every iterable\'s item must be unique.';

        return [
            'incorrect input, integer' => [1, new Unique(), ['' => [$incorrectInputMessage]]],
            'incorrect input, object' => [new stdClass(), new Unique(), ['' => [$incorrectInputMessage]]],
            'incorrect input, custom message' => [
                ['data' => 1],
                ['data' => new Unique(incorrectInputMessage: 'Attribute - {attribute}, type - {type}.')],
                ['data' => ['Attribute - data, type - int.']],
            ],
            'incorrect item value, array' => [[1, [], 2], new Unique(), ['' => [$incorrectItemValueMessage]]],
            'incorrect item value, object not implemeting \Stringable' => [
                [1, new stdClass(), 2],
                new Unique(),
                ['' => [$incorrectItemValueMessage]],
            ],
            'incorrect item value, custom message' => [
                ['data' => [1, [], 2]],
                ['data' => new Unique(incorrectItemValueMessage: 'Attribute - {attribute}, type - {type}.')],
                ['data' => ['Attribute - data, type - array.']],
            ],
            'null' => [[null, null], new Unique(), ['' => [$message]]],
            'strings' => [['a', 'b', 'a', 'c'], new Unique(), ['' => [$message]]],
            'integers' => [[1, 2, 1, 3], new Unique(), ['' => [$message]]],
            'floats' => [[1.5, 2.5, 1.5, 3.5], new Unique(), ['' => [$message]]],
            'boolean values' => [[false, true, false], new Unique(), ['' => [$message]]],
            'stringable values' => [
                [
                    new class () implements Stringable {
                        public function __toString()
                        {
                            return 'a';
                        }
                    },
                    new class () implements Stringable {
                        public function __toString()
                        {
                            return 'b';
                        }
                    },
                    new class () implements Stringable {
                        public function __toString()
                        {
                            return 'a';
                        }
                    },
                    new class () implements Stringable {
                        public function __toString()
                        {
                            return 'c';
                        }
                    },
                ],
                new Unique(),
                ['' => [$message]],
            ],
            'datetime values' => [
                [
                    new DateTime('2024-04-10 14:05:01'),
                    new DateTime('2024-04-10 14:05:02'),
                    new DateTime('2024-04-10 14:05:01'),
                    new DateTime('2024-04-10 14:05:03'),
                ],
                new Unique(),
                ['' => [$message]],
            ],
            'mix, string and stringable' => [
                [
                    'a',
                    'b',
                    new class () implements Stringable {
                        public function __toString()
                        {
                            return 'a';
                        }
                    },
                    'c',
                ],
                new Unique(),
                ['' => [$message]],
            ],
            'custom message' => [
                ['data' => [1, 2, 1, 3]],
                ['data' => new Unique(message: 'Attribute - {attribute}.')],
                ['data' => ['Attribute - data.']],
            ]
        ];
    }

    public function testSkipOnError(): void
    {
        $this->testSkipOnErrorInternal(new Unique(), new Unique(skipOnError: true));
    }

    public function testWhen(): void
    {
        $when = static fn (mixed $value): bool => $value !== null;
        $this->testWhenInternal(new Unique(), new Unique(when: $when));
    }

    protected function getDifferentRuleInHandlerItems(): array
    {
        return [Unique::class, UniqueHandler::class];
    }
}
