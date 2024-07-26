<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use InvalidArgumentException;
use Yiisoft\Validator\EmptyCondition\NeverEmpty;
use Yiisoft\Validator\Rule\AtLeast;
use Yiisoft\Validator\Rule\AtLeastHandler;
use Yiisoft\Validator\Tests\Rule\Base\DifferentRuleInHandlerTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\RuleTestCase;
use Yiisoft\Validator\Tests\Rule\Base\RuleWithOptionsTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\SkipOnErrorTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\WhenTestTrait;
use Yiisoft\Validator\Tests\Support\Data\AtLeastDto;
use Yiisoft\Validator\ValidationContext;

final class AtLeastTest extends RuleTestCase
{
    use DifferentRuleInHandlerTestTrait;
    use RuleWithOptionsTestTrait;
    use SkipOnErrorTestTrait;
    use WhenTestTrait;

    public function testMinGreaterThanPropertiesCount(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('$min must be no greater than amount of $properties.');
        new AtLeast(['prop'], min: 2);
    }

    public function testGetName(): void
    {
        $rule = new AtLeast(['prop']);
        $this->assertSame(AtLeast::class, $rule->getName());
    }

    public function testPropertyIsNull(): void
    {
        $data = ['prop1' => 1, 'prop2' => null];
        $rule = new AtLeast(
            properties: ['prop1', 'prop2'],
            min: 2,
            message: 'properties - {properties}, property - {property}, min - {min}.',
        );
        $context = new ValidationContext();

        (new AtLeastHandler())->validate($data, $rule, $context);

        $this->assertNull($context->getPropertyLabel());
    }

    public function dataOptions(): array
    {
        return [
            [
                new AtLeast(['prop1', 'prop2']),
                [
                    'properties' => [
                        'prop1',
                        'prop2',
                    ],
                    'min' => 1,
                    'incorrectInputMessage' => [
                        'template' => '{Property} must be an array or an object.',
                        'parameters' => [],
                    ],
                    'message' => [
                        'template' => 'At least {min, number} {min, plural, one{property} other{properties}} from ' .
                            'this list must be filled: {properties}.',
                        'parameters' => ['min' => 1],
                    ],
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                new AtLeast(['prop1', 'prop2'], min: 2),
                [
                    'properties' => [
                        'prop1',
                        'prop2',
                    ],
                    'min' => 2,
                    'incorrectInputMessage' => [
                        'template' => '{Property} must be an array or an object.',
                        'parameters' => [],
                    ],
                    'message' => [
                        'template' => 'At least {min, number} {min, plural, one{property} other{properties}} from ' .
                            'this list must be filled: {properties}.',
                        'parameters' => ['min' => 2],
                    ],
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            'callable skip on empty' => [
                new AtLeast(['prop1', 'prop2'], skipOnEmpty: new NeverEmpty()),
                [
                    'properties' => [
                        'prop1',
                        'prop2',
                    ],
                    'min' => 1,
                    'incorrectInputMessage' => [
                        'template' => '{Property} must be an array or an object.',
                        'parameters' => [],
                    ],
                    'message' => [
                        'template' => 'At least {min, number} {min, plural, one{property} other{properties}} from ' .
                            'this list must be filled: {properties}.',
                        'parameters' => ['min' => 1],
                    ],
                    'skipOnEmpty' => null,
                    'skipOnError' => false,
                ],
            ],
        ];
    }

    public function dataValidationPassed(): array
    {
        return [
            [
                new class () {
                    public $prop1 = 1;
                    public $prop2 = null;
                },
                [new AtLeast(['prop1', 'prop2'])],
            ],
            [
                new class () {
                    public $prop1 = null;
                    public $prop2 = 1;
                },
                [new AtLeast(['prop2'])],
            ],
            [
                new class () {
                    private int $prop1 = 1;
                    private $prop2 = null;
                },
                [new AtLeast(['prop1', 'prop2'])],
            ],
            [
                ['prop1' => 1, 'prop2' => null],
                [new AtLeast(['prop1', 'prop2'])],
            ],
            [
                ['prop1' => null, 'prop2' => 1],
                [new AtLeast(['prop2'])],
            ],
            [
                new class () {
                    public $obj;

                    public function __construct()
                    {
                        $this->obj = new class () {
                            public $prop1 = 1;
                            public $prop2 = null;
                        };
                    }
                },
                ['obj' => new AtLeast(['prop1', 'prop2'])],
            ],
            [
                new class () {
                    public $obj;

                    public function __construct()
                    {
                        $this->obj = new class () {
                            public $prop1 = null;
                            public $prop2 = 1;
                        };
                    }
                },
                ['obj' => new AtLeast(['prop2'])],
            ],
            [
                ['obj' => ['prop1' => 1, 'prop2' => null]],
                ['obj' => new AtLeast(['prop1', 'prop2'])],
            ],
            [
                ['obj' => ['prop1' => null, 'prop2' => 1]],
                ['obj' => new AtLeast(['prop2'])],
            ],
            'more than "min" properties are filled' => [
                ['prop1' => 1, 'prop2' => 2],
                [new AtLeast(['prop1', 'prop2'])],
            ],
            'min equals amount of properties' => [
                ['prop1' => 1, 'prop2' => 2],
                [new AtLeast(['prop1', 'prop2'], min: 2)],
            ],
            'min equals amount of properties, 0' => [
                [],
                [new AtLeast([], min: 0)],
            ],
            'class property' => [
                new AtLeastDto(1),
            ],
        ];
    }

    public function dataValidationFailed(): array
    {
        $class = new class () {
            public $prop1 = 1;
            public $prop2 = null;
        };
        $array = ['prop1' => 1, 'prop2' => null];

        return [
            'incorrect input' => [
                1,
                [new AtLeast(['prop2'])],
                ['' => ['Value must be an array or an object.']],
            ],
            'custom incorrect input message' => [
                1,
                [new AtLeast(['prop2'], incorrectInputMessage: 'Custom incorrect input message.')],
                ['' => ['Custom incorrect input message.']],
            ],
            'custom incorrect input message with parameters' => [
                1,
                [new AtLeast(['prop2'], incorrectInputMessage: 'Property - {Property}, type - {type}.')],
                ['' => ['Property - Value, type - int.']],
            ],
            'custom incorrect input message with parameters, property set' => [
                ['property' => 1],
                [
                    'property' => new AtLeast(
                        ['prop2'],
                        incorrectInputMessage: 'Property - {property}, type - {type}.',
                    ),
                ],
                ['property' => ['Property - property, type - int.']],
            ],
            'object' => [
                $class,
                [new AtLeast(['prop2'])],
                ['' => ['At least 1 property from this list must be filled: "prop2".']],
            ],
            'object, custom min' => [
                $class,
                [new AtLeast(['prop1', 'prop2'], min: 2)],
                ['' => ['At least 2 properties from this list must be filled: "prop1", "prop2".']],
            ],
            'array' => [
                $array,
                [new AtLeast(['prop2'])],
                ['' => ['At least 1 property from this list must be filled: "prop2".']],
            ],
            'array, custom min' => [
                $array,
                [new AtLeast(['prop1', 'prop2'], min: 2)],
                ['' => ['At least 2 properties from this list must be filled: "prop1", "prop2".']],
            ],
            'custom message' => [
                $class,
                [new AtLeast(['prop1', 'prop2'], min: 2, message: 'Custom message.')],
                ['' => ['Custom message.']],
            ],
            'custom message with parameters' => [
                $class,
                [new AtLeast(['prop1', 'prop2'], min: 2, message: 'Properties - {Properties}, min - {min}.')],
                ['' => ['Properties - "Prop1", "Prop2", min - 2.']],
            ],
            'custom message with parameters, property set' => [
                ['data' => $class],
                ['data' => new AtLeast(['prop1', 'prop2'], min: 2, message: 'Properties - {properties}, min - {min}.')],
                ['data' => ['Properties - "prop1", "prop2", min - 2.']],
            ],
            'class property, translation' => [
                new AtLeastDto(),
                null,
                ['' => ['At least 1 property from this list must be filled: "A", "B", "C".']],
            ],
        ];
    }

    public function testSkipOnError(): void
    {
        $this->testSkipOnErrorInternal(new AtLeast(['prop']), new AtLeast(['prop'], skipOnError: true));
    }

    public function testWhen(): void
    {
        $when = static fn (mixed $value): bool => $value !== null;
        $this->testWhenInternal(new AtLeast(['prop']), new AtLeast(['prop'], when: $when));
    }

    protected function getDifferentRuleInHandlerItems(): array
    {
        return [AtLeast::class, AtLeastHandler::class];
    }
}
