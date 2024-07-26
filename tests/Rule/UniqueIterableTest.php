<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use DateTime;
use stdClass;
use Stringable;
use Yiisoft\Validator\PropertyTranslator\ArrayPropertyTranslator;
use Yiisoft\Validator\PropertyTranslatorInterface;
use Yiisoft\Validator\PropertyTranslatorProviderInterface;
use Yiisoft\Validator\Rule\UniqueIterable;
use Yiisoft\Validator\Rule\UniqueIterableHandler;
use Yiisoft\Validator\RulesProviderInterface;
use Yiisoft\Validator\Tests\Rule\Base\DifferentRuleInHandlerTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\RuleTestCase;
use Yiisoft\Validator\Tests\Rule\Base\RuleWithOptionsTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\SkipOnErrorTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\WhenTestTrait;

final class UniqueIterableTest extends RuleTestCase
{
    use DifferentRuleInHandlerTestTrait;
    use RuleWithOptionsTestTrait;
    use SkipOnErrorTestTrait;
    use WhenTestTrait;

    public function testGetName(): void
    {
        $rule = new UniqueIterable();
        $this->assertSame(UniqueIterable::class, $rule->getName());
    }

    public function dataOptions(): array
    {
        return [
            'default' => [
                new UniqueIterable(),
                [
                    'incorrectInputMessage' => [
                        'template' => '{Property} must be array or iterable.',
                        'parameters' => [],
                    ],
                    'incorrectItemValueMessage' => [
                        'template' => 'The allowed types for iterable\'s item values of {property} are integer, ' .
                            'float, string, boolean and object implementing \Stringable or \DateTimeInterface.',
                        'parameters' => [],
                    ],
                    'differentTypesMessage' => [
                        'template' => 'All iterable items of {property} must have the same type.',
                        'parameters' => [],
                    ],
                    'message' => [
                        'template' => 'Every iterable\'s item of {property} must be unique.',
                        'parameters' => [],
                    ],
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            'custom' => [
                new UniqueIterable(
                    incorrectInputMessage: 'Custom message 1.',
                    incorrectItemValueMessage: 'Custom message 2.',
                    differentTypesMessage: 'Custom message 3.',
                    message: 'Custom message 4.',
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
                        'template' => 'Custom message 3.',
                        'parameters' => [],
                    ],
                    'message' => [
                        'template' => 'Custom message 4.',
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
            'strings' => [['a', 'b'], new UniqueIterable()],
            'integers' => [[1, 2], new UniqueIterable()],
            'floats' => [[1.5, 2.5], new UniqueIterable()],
            'boolean values' => [[false, true], new UniqueIterable()],
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
                new UniqueIterable(),
            ],
            'datetime values' => [
                [new DateTime('2024-04-10 14:05:01'), new DateTime('2024-04-10 14:05:02')],
                new UniqueIterable(),
            ],
            'using as attribute' => [
                new class () {
                    #[UniqueIterable]
                    private array $data = [1, 2];
                },
                null,
            ],
        ];
    }

    public function dataValidationFailed(): array
    {
        $incorrectInputMessage = 'Value must be array or iterable.';
        $incorrectItemValueMessage = 'The allowed types for iterable\'s item values of value are integer, float, ' .
            'string, boolean and object implementing \Stringable or \DateTimeInterface.';
        $message = 'Every iterable\'s item of value must be unique.';

        return [
            'incorrect input, integer' => [1, new UniqueIterable(), ['' => [$incorrectInputMessage]]],
            'incorrect input, object' => [new stdClass(), new UniqueIterable(), ['' => [$incorrectInputMessage]]],
            'incorrect input, custom message' => [
                ['data' => 1],
                ['data' => new UniqueIterable(incorrectInputMessage: 'Property - {property}, type - {type}.')],
                ['data' => ['Property - data, type - int.']],
            ],
            'incorrect input, custom message, translated property' => [
                new class () implements RulesProviderInterface, PropertyTranslatorProviderInterface {
                    public function __construct(
                        public int $data = 1,
                    ) {
                    }

                    public function getPropertyLabels(): array
                    {
                        return [
                            'data' => 'Данные',
                        ];
                    }

                    public function getPropertyTranslator(): ?PropertyTranslatorInterface
                    {
                        return new ArrayPropertyTranslator($this->getPropertyLabels());
                    }

                    public function getRules(): array
                    {
                        return ['data' => new UniqueIterable(incorrectInputMessage: '"{property}" - неитерируемое значение.')];
                    }
                },
                null,
                ['data' => ['"Данные" - неитерируемое значение.']],
            ],
            'incorrect item value, null' => [[null], new UniqueIterable(), ['' => [$incorrectItemValueMessage]]],
            'incorrect item value, array' => [[1, [], 2], new UniqueIterable(), ['' => [$incorrectItemValueMessage]]],
            'incorrect item value, object not implemeting \Stringable' => [
                [1, new stdClass(), 2],
                new UniqueIterable(),
                ['' => [$incorrectItemValueMessage]],
            ],
            'incorrect item value, custom message' => [
                ['data' => [1, [], 2]],
                ['data' => new UniqueIterable(incorrectItemValueMessage: 'Property - {property}, type - {type}.')],
                ['data' => ['Property - data, type - array.']],
            ],
            'incorrect item value, custom message, translated property' => [
                new class () implements RulesProviderInterface, PropertyTranslatorProviderInterface {
                    public function __construct(
                        public array $data = [1, 2, [], 3],
                    ) {
                    }

                    public function getPropertyLabels(): array
                    {
                        return [
                            'data' => 'Данные',
                        ];
                    }

                    public function getPropertyTranslator(): ?PropertyTranslatorInterface
                    {
                        return new ArrayPropertyTranslator($this->getPropertyLabels());
                    }

                    public function getRules(): array
                    {
                        return [
                            'data' => new UniqueIterable(
                                incorrectItemValueMessage: '"{property}" - в списке есть недопустимое значение.',
                            ),
                        ];
                    }
                },
                null,
                ['data' => ['"Данные" - в списке есть недопустимое значение.']],
            ],
            'strings' => [['a', 'b', 'a', 'c'], new UniqueIterable(), ['' => [$message]]],
            'integers' => [[1, 2, 1, 3], new UniqueIterable(), ['' => [$message]]],
            'floats' => [[1.5, 2.5, 1.5, 3.5], new UniqueIterable(), ['' => [$message]]],
            'boolean values' => [[false, true, false], new UniqueIterable(), ['' => [$message]]],
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
                new UniqueIterable(),
                ['' => [$message]],
            ],
            'datetime values' => [
                [
                    new DateTime('2024-04-10 14:05:01'),
                    new DateTime('2024-04-10 14:05:02'),
                    new DateTime('2024-04-10 14:05:01'),
                    new DateTime('2024-04-10 14:05:03'),
                ],
                new UniqueIterable(),
                ['' => [$message]],
            ],
            'different types' => [
                ['data' => [1, '2', 3]],
                ['data' => new UniqueIterable()],
                ['data' => ['All iterable items of data must have the same type.']],
            ],
            'different types, custom message' => [
                ['data' => [1, '2', 3]],
                ['data' => new UniqueIterable(differentTypesMessage: 'Property - {property}.')],
                ['data' => ['Property - data.']],
            ],
            'different types, translated property' => [
                new class () implements RulesProviderInterface, PropertyTranslatorProviderInterface {
                    public function __construct(
                        public array $data = [1, '2', 3],
                    ) {
                    }

                    public function getPropertyLabels(): array
                    {
                        return [
                            'data' => 'Данные',
                        ];
                    }

                    public function getPropertyTranslator(): ?PropertyTranslatorInterface
                    {
                        return new ArrayPropertyTranslator($this->getPropertyLabels());
                    }

                    public function getRules(): array
                    {
                        return [
                            'data' => new UniqueIterable(
                                differentTypesMessage: '"{property}" - в списке есть элементы разных типов.',
                            ),
                        ];
                    }
                },
                null,
                ['data' => ['"Данные" - в списке есть элементы разных типов.']],
            ],
            'custom message' => [
                ['data' => [1, 2, 1, 3]],
                ['data' => new UniqueIterable(message: 'Property - {property}.')],
                ['data' => ['Property - data.']],
            ],
            'custom message, translated property' => [
                new class () implements RulesProviderInterface, PropertyTranslatorProviderInterface {
                    public function __construct(
                        public array $data = [1, 2, 1, 3],
                    ) {
                    }

                    public function getPropertyLabels(): array
                    {
                        return [
                            'data' => 'Данные',
                        ];
                    }

                    public function getPropertyTranslator(): ?PropertyTranslatorInterface
                    {
                        return new ArrayPropertyTranslator($this->getPropertyLabels());
                    }

                    public function getRules(): array
                    {
                        return ['data' => new UniqueIterable(message: '"{property}" - в списке есть дубликаты.')];
                    }
                },
                null,
                ['data' => ['"Данные" - в списке есть дубликаты.']],
            ],
            'using as attribute' => [
                new class () {
                    #[UniqueIterable]
                    private array $data = [1, 2, 1, 3];
                },
                null,
                ['data' => ['Every iterable\'s item of data must be unique.']],
            ],
        ];
    }

    public function testSkipOnError(): void
    {
        $this->testSkipOnErrorInternal(new UniqueIterable(), new UniqueIterable(skipOnError: true));
    }

    public function testWhen(): void
    {
        $when = static fn (mixed $value): bool => $value !== null;
        $this->testWhenInternal(new UniqueIterable(), new UniqueIterable(when: $when));
    }

    protected function getDifferentRuleInHandlerItems(): array
    {
        return [UniqueIterable::class, UniqueIterableHandler::class];
    }
}
