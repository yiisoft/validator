<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use Yiisoft\Validator\EmptyCondition\NeverEmpty;
use Yiisoft\Validator\Rule\FilledOnlyOneOf;
use Yiisoft\Validator\Rule\FilledOnlyOneOfHandler;
use Yiisoft\Validator\Tests\Rule\Base\DifferentRuleInHandlerTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\RuleTestCase;
use Yiisoft\Validator\Tests\Rule\Base\RuleWithOptionsTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\SkipOnErrorTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\WhenTestTrait;
use Yiisoft\Validator\Tests\Support\Data\FilledOnlyOneOfDto;

final class FilledOnlyOneOfTest extends RuleTestCase
{
    use DifferentRuleInHandlerTestTrait;
    use RuleWithOptionsTestTrait;
    use SkipOnErrorTestTrait;
    use WhenTestTrait;

    public function testGetName(): void
    {
        $rule = new FilledOnlyOneOf([]);
        $this->assertSame(FilledOnlyOneOf::class, $rule->getName());
    }

    public static function dataOptions(): array
    {
        return [
            'default' => [
                new FilledOnlyOneOf(['prop1', 'prop2']),
                [
                    'properties' => [
                        'prop1',
                        'prop2',
                    ],
                    'incorrectInputMessage' => [
                        'template' => '{Property} must be an array or an object. {type} given.',
                        'parameters' => [],
                    ],
                    'message' => [
                        'template' => 'Exactly 1 property from this list must be filled for {property}: {properties}.',
                        'parameters' => [],
                    ],
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            'custom' => [
                new FilledOnlyOneOf(
                    ['prop1', 'prop2'],
                    incorrectInputMessage: 'Custom message 1.',
                    message: 'Custom message 2.',
                    skipOnEmpty: true,
                    skipOnError: true,
                ),
                [
                    'properties' => [
                        'prop1',
                        'prop2',
                    ],
                    'incorrectInputMessage' => [
                        'template' => 'Custom message 1.',
                        'parameters' => [],
                    ],
                    'message' => [
                        'template' => 'Custom message 2.',
                        'parameters' => [],
                    ],
                    'skipOnEmpty' => true,
                    'skipOnError' => true,
                ],
            ],
            'callable skip on empty' => [
                new FilledOnlyOneOf(['prop1', 'prop2'], skipOnEmpty: new NeverEmpty()),
                [
                    'properties' => [
                        'prop1',
                        'prop2',
                    ],
                    'incorrectInputMessage' => [
                        'template' => '{Property} must be an array or an object. {type} given.',
                        'parameters' => [],
                    ],
                    'message' => [
                        'template' => 'Exactly 1 property from this list must be filled for {property}: {properties}.',
                        'parameters' => [],
                    ],
                    'skipOnEmpty' => null,
                    'skipOnError' => false,
                ],
            ],
        ];
    }

    public static function dataValidationPassed(): array
    {
        return [
            [
                new class {
                    public $prop1 = 1;
                    public $prop2 = null;
                },
                [new FilledOnlyOneOf(['prop1', 'prop2'])],
            ],
            [
                new class {
                    public $prop1 = null;
                    public $prop2 = 1;
                },
                [new FilledOnlyOneOf(['prop1', 'prop2'])],
            ],
            [
                new class {
                    private int $prop1 = 1;
                    private $prop2 = null;
                },
                [new FilledOnlyOneOf(['prop1', 'prop2'])],
            ],
            [
                ['prop1' => 1, 'prop2' => null],
                [new FilledOnlyOneOf(['prop1', 'prop2'])],
            ],
            [
                ['prop1' => null, 'prop2' => 1],
                [new FilledOnlyOneOf(['prop1', 'prop2'])],
            ],
            [
                new class {
                    public $obj;

                    public function __construct()
                    {
                        $this->obj = new class {
                            public $prop1 = 1;
                            public $prop2 = null;
                        };
                    }
                },
                ['obj' => new FilledOnlyOneOf(['prop1', 'prop2'])],
            ],
            [
                new class {
                    public $obj;

                    public function __construct()
                    {
                        $this->obj = new class {
                            public $prop1 = null;
                            public $prop2 = 1;
                        };
                    }
                },
                ['obj' => new FilledOnlyOneOf(['prop1', 'prop2'])],
            ],
            [
                ['obj' => ['prop1' => 1, 'prop2' => null]],
                ['obj' => new FilledOnlyOneOf(['prop1', 'prop2'])],
            ],
            [
                ['obj' => ['prop1' => null, 'prop2' => 1]],
                ['obj' => new FilledOnlyOneOf(['prop1', 'prop2'])],
            ],
            'class property, translation' => [
                new FilledOnlyOneOfDto(1),
            ],
        ];
    }

    public static function dataValidationFailed(): array
    {
        $object = new class {
            public $prop1 = null;
            public $prop2 = null;
        };
        $array = ['prop1' => null, 'prop2' => null];

        return [
            'incorrect input' => [
                1,
                [new FilledOnlyOneOf(['prop1', 'prop2'])],
                ['' => ['Value must be an array or an object. int given.']],
            ],
            'custom incorrect input message' => [
                1,
                [new FilledOnlyOneOf(['prop1', 'prop2'], incorrectInputMessage: 'Custom incorrect input message.')],
                ['' => ['Custom incorrect input message.']],
            ],
            'custom incorrect input message with parameters' => [
                1,
                [new FilledOnlyOneOf(['prop1', 'prop2'], incorrectInputMessage: 'Property - {property}, type - {type}.')],
                ['' => ['Property - value, type - int.']],
            ],
            'custom incorrect input message with parameters, property set' => [
                ['property' => 1],
                [
                    'property' => new FilledOnlyOneOf(
                        ['prop1', 'prop2'],
                        incorrectInputMessage: 'Property - {property}, type - {type}.',
                    ),
                ],
                ['property' => ['Property - property, type - int.']],
            ],
            'object' => [
                $object,
                [new FilledOnlyOneOf(['prop1', 'prop2'])],
                ['' => ['Exactly 1 property from this list must be filled for value: "prop1", "prop2".']],
            ],
            'array' => [
                $array,
                [new FilledOnlyOneOf(['prop1', 'prop2'])],
                ['' => ['Exactly 1 property from this list must be filled for value: "prop1", "prop2".']],
            ],
            'more than 1 property is filled' => [
                ['prop1' => 1, 'prop2' => 2],
                [new FilledOnlyOneOf(['prop1', 'prop2'])],
                ['' => ['Exactly 1 property from this list must be filled for value: "prop1", "prop2".']],
            ],
            'custom message' => [
                $object,
                [new FilledOnlyOneOf(['prop1', 'prop2'], message: 'Custom message.')],
                ['' => ['Custom message.']],
            ],
            'custom message with parameters' => [
                $object,
                [new FilledOnlyOneOf(['prop1', 'prop2'], message: 'Properties - {Properties}.')],
                ['' => ['Properties - "Prop1", "Prop2".']],
            ],
            'custom message with parameters, property set' => [
                ['data' => $object],
                ['data' => new FilledOnlyOneOf(['prop1', 'prop2'], message: 'Properties - {properties}.')],
                ['data' => ['Properties - "prop1", "prop2".']],
            ],
            'class property' => [
                new FilledOnlyOneOfDto(),
                null,
                ['' => ['Exactly 1 property from this list must be filled for value: "A", "B", "C".']],
            ],
        ];
    }

    public function testSkipOnError(): void
    {
        $this->testSkipOnErrorInternal(new FilledOnlyOneOf([]), new FilledOnlyOneOf([], skipOnError: true));
    }

    public function testWhen(): void
    {
        $when = static fn(mixed $value): bool => $value !== null;
        $this->testWhenInternal(new FilledOnlyOneOf([]), new FilledOnlyOneOf([], when: $when));
    }

    protected function getDifferentRuleInHandlerItems(): array
    {
        return [FilledOnlyOneOf::class, FilledOnlyOneOfHandler::class];
    }
}
