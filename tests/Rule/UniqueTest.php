<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use DateTime;
use stdClass;
use Stringable;
use Yiisoft\Validator\AttributeTranslator\ArrayAttributeTranslator;
use Yiisoft\Validator\AttributeTranslatorInterface;
use Yiisoft\Validator\AttributeTranslatorProviderInterface;
use Yiisoft\Validator\Rule\Unique;
use Yiisoft\Validator\Rule\UniqueHandler;
use Yiisoft\Validator\RulesProviderInterface;
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
                            'boolean and object implementing \Stringable or \DateTimeInterface.',
                        'parameters' => [],
                    ],
                    'differentTypesMessage' => [
                        'template' => 'All iterable items must have the same type.',
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
                    'differentTypesMessage' => [
                        'template' => 'All iterable items must have the same type.',
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
            'using as attribute' => [
                new class () {
                    #[Unique]
                    private array $data = [1, 2];
                },
                null,
            ],
        ];
    }

    public function dataValidationFailed(): array
    {
        $incorrectInputMessage = 'Value must be array or iterable.';
        $incorrectItemValueMessage = 'The allowed types for iterable\'s item values are integer, float, string, ' .
            'boolean and object implementing \Stringable or \DateTimeInterface.';
        $differentTypesMessage = 'All iterable items must have the same type.';
        $message = 'Every iterable\'s item must be unique.';

        return [
            'incorrect input, integer' => [1, new Unique(), ['' => [$incorrectInputMessage]]],
            'incorrect input, object' => [new stdClass(), new Unique(), ['' => [$incorrectInputMessage]]],
            'incorrect input, custom message' => [
                ['data' => 1],
                ['data' => new Unique(incorrectInputMessage: 'Attribute - {attribute}, type - {type}.')],
                ['data' => ['Attribute - data, type - int.']],
            ],
            'incorrect input, custom message, translated attribute' => [
                new class () implements RulesProviderInterface, AttributeTranslatorProviderInterface {
                    public function __construct(
                        public int $data = 1,
                    ) {
                    }

                    public function getAttributeLabels(): array
                    {
                        return [
                            'data' => 'Данные',
                        ];
                    }

                    public function getAttributeTranslator(): ?AttributeTranslatorInterface
                    {
                        return new ArrayAttributeTranslator($this->getAttributeLabels());
                    }

                    public function getRules(): array
                    {
                        return ['data' => new Unique(incorrectInputMessage: '"{attribute}" - неитерируемое значение.')];
                    }
                },
                null,
                ['data' => ['"Данные" - неитерируемое значение.']],
            ],
            'incorrect item value, null' => [[null], new Unique(), ['' => [$incorrectItemValueMessage]]],
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
            'incorrect item value, custom message, translated attribute' => [
                new class () implements RulesProviderInterface, AttributeTranslatorProviderInterface {
                    public function __construct(
                        public array $data = [1, 2, [], 3],
                    ) {
                    }

                    public function getAttributeLabels(): array
                    {
                        return [
                            'data' => 'Данные',
                        ];
                    }

                    public function getAttributeTranslator(): ?AttributeTranslatorInterface
                    {
                        return new ArrayAttributeTranslator($this->getAttributeLabels());
                    }

                    public function getRules(): array
                    {
                        return [
                            'data' => new Unique(
                                incorrectItemValueMessage: '"{attribute}" - в списке есть недопустимое значение.',
                            ),
                        ];
                    }
                },
                null,
                ['data' => ['"Данные" - в списке есть недопустимое значение.']],
            ],
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
            'different types' => [
                ['data' => [1, '2', 3]],
                ['data' => new Unique()],
                ['data' => [$differentTypesMessage]],
            ],
            'different types, custom message' => [
                ['data' => [1, '2', 3]],
                ['data' => new Unique(differentTypesMessage: 'Attribute - {attribute}.')],
                ['data' => ['Attribute - data.']],
            ],
            'different types, translated attribute' => [
                new class () implements RulesProviderInterface, AttributeTranslatorProviderInterface {
                    public function __construct(
                        public array $data = [1, '2', 3],
                    ) {
                    }

                    public function getAttributeLabels(): array
                    {
                        return [
                            'data' => 'Данные',
                        ];
                    }

                    public function getAttributeTranslator(): ?AttributeTranslatorInterface
                    {
                        return new ArrayAttributeTranslator($this->getAttributeLabels());
                    }

                    public function getRules(): array
                    {
                        return [
                            'data' => new Unique(
                                differentTypesMessage: '"{attribute}" - в списке есть элементы разных типов.',
                            ),
                        ];
                    }
                },
                null,
                ['data' => ['"Данные" - в списке есть элементы разных типов.']],
            ],
            'custom message' => [
                ['data' => [1, 2, 1, 3]],
                ['data' => new Unique(message: 'Attribute - {attribute}.')],
                ['data' => ['Attribute - data.']],
            ],
            'custom message, translated attribute' => [
                new class () implements RulesProviderInterface, AttributeTranslatorProviderInterface {
                    public function __construct(
                        public array $data = [1, 2, 1, 3],
                    ) {
                    }

                    public function getAttributeLabels(): array
                    {
                        return [
                            'data' => 'Данные',
                        ];
                    }

                    public function getAttributeTranslator(): ?AttributeTranslatorInterface
                    {
                        return new ArrayAttributeTranslator($this->getAttributeLabels());
                    }

                    public function getRules(): array
                    {
                        return ['data' => new Unique(message: '"{attribute}" - в списке есть дубликаты.')];
                    }
                },
                null,
                ['data' => ['"Данные" - в списке есть дубликаты.']],
            ],
            'using as attribute' => [
                new class () {
                    #[Unique]
                    private array $data = [1, 2, 1, 3];
                },
                null,
                ['data' => [$message]],
            ],
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
