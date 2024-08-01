<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use InvalidArgumentException;
use Yiisoft\Validator\EmptyCondition\NeverEmpty;
use Yiisoft\Validator\Rule\FilledAtLeast;
use Yiisoft\Validator\Rule\FilledAtLeastHandler;
use Yiisoft\Validator\Tests\Rule\Base\DifferentRuleInHandlerTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\RuleTestCase;
use Yiisoft\Validator\Tests\Rule\Base\RuleWithOptionsTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\SkipOnErrorTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\WhenTestTrait;
use Yiisoft\Validator\Tests\Support\Data\AtLeastDto;
use Yiisoft\Validator\ValidationContext;

final class FilledAtLeastTest extends RuleTestCase
{
    use DifferentRuleInHandlerTestTrait;
    use RuleWithOptionsTestTrait;
    use SkipOnErrorTestTrait;
    use WhenTestTrait;

    public function testMinGreaterThanPropertiesCount(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('$min must be no greater than amount of $properties.');
        new FilledAtLeast(['prop'], min: 2);
    }

    public function testGetName(): void
    {
        $rule = new FilledAtLeast(['prop']);
        $this->assertSame(FilledAtLeast::class, $rule->getName());
    }

    public function testPropertyIsNull(): void
    {
        $data = ['prop1' => 1, 'prop2' => null];
        $rule = new FilledAtLeast(
            properties: ['prop1', 'prop2'],
            min: 2,
            message: 'properties - {properties}, property - {property}, min - {min}.',
        );
        $context = new ValidationContext();

        (new FilledAtLeastHandler())->validate($data, $rule, $context);

        $this->assertNull($context->getPropertyLabel());
    }

    public function dataOptions(): array
    {
        return [
            'default' => [
                new FilledAtLeast(['prop1', 'prop2']),
                [
                    'properties' => [
                        'prop1',
                        'prop2',
                    ],
                    'min' => 1,
                    'incorrectInputMessage' => [
                        'template' => '{Property} must be an array or an object. {type} given.',
                        'parameters' => [],
                    ],
                    'message' => [
                        'template' => 'At least {min, number} {min, plural, one{property} other{properties}} from ' .
                            'this list must be filled for {property}: {properties}.',
                        'parameters' => ['min' => 1],
                    ],
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            'custom' => [
                new FilledAtLeast(
                    ['prop1', 'prop2'],
                    min: 2,
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
                    'min' => 2,
                    'incorrectInputMessage' => [
                        'template' => 'Custom message 1.',
                        'parameters' => [],
                    ],
                    'message' => [
                        'template' => 'Custom message 2.',
                        'parameters' => ['min' => 2],
                    ],
                    'skipOnEmpty' => true,
                    'skipOnError' => true,
                ],
            ],
            'callable skip on empty' => [
                new FilledAtLeast(['prop1', 'prop2'], skipOnEmpty: new NeverEmpty()),
                [
                    'properties' => [
                        'prop1',
                        'prop2',
                    ],
                    'min' => 1,
                    'incorrectInputMessage' => [
                        'template' => '{Property} must be an array or an object. {type} given.',
                        'parameters' => [],
                    ],
                    'message' => [
                        'template' => 'At least {min, number} {min, plural, one{property} other{properties}} from ' .
                            'this list must be filled for {property}: {properties}.',
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
                [new FilledAtLeast(['prop1', 'prop2'])],
            ],
            [
                new class () {
                    public $prop1 = null;
                    public $prop2 = 1;
                },
                [new FilledAtLeast(['prop2'])],
            ],
            [
                new class () {
                    private int $prop1 = 1;
                    private $prop2 = null;
                },
                [new FilledAtLeast(['prop1', 'prop2'])],
            ],
            [
                ['prop1' => 1, 'prop2' => null],
                [new FilledAtLeast(['prop1', 'prop2'])],
            ],
            [
                ['prop1' => null, 'prop2' => 1],
                [new FilledAtLeast(['prop2'])],
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
                ['obj' => new FilledAtLeast(['prop1', 'prop2'])],
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
                ['obj' => new FilledAtLeast(['prop2'])],
            ],
            [
                ['obj' => ['prop1' => 1, 'prop2' => null]],
                ['obj' => new FilledAtLeast(['prop1', 'prop2'])],
            ],
            [
                ['obj' => ['prop1' => null, 'prop2' => 1]],
                ['obj' => new FilledAtLeast(['prop2'])],
            ],
            'more than "min" properties are filled' => [
                ['prop1' => 1, 'prop2' => 2],
                [new FilledAtLeast(['prop1', 'prop2'])],
            ],
            'min equals amount of properties' => [
                ['prop1' => 1, 'prop2' => 2],
                [new FilledAtLeast(['prop1', 'prop2'], min: 2)],
            ],
            'min equals amount of properties, 0' => [
                [],
                [new FilledAtLeast([], min: 0)],
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
                [new FilledAtLeast(['prop2'])],
                ['' => ['Value must be an array or an object. int given.']],
            ],
            'custom incorrect input message' => [
                1,
                [new FilledAtLeast(['prop2'], incorrectInputMessage: 'Custom incorrect input message.')],
                ['' => ['Custom incorrect input message.']],
            ],
            'custom incorrect input message with parameters' => [
                1,
                [new FilledAtLeast(['prop2'], incorrectInputMessage: 'Property - {Property}, type - {type}.')],
                ['' => ['Property - Value, type - int.']],
            ],
            'custom incorrect input message with parameters, property set' => [
                ['property' => 1],
                [
                    'property' => new FilledAtLeast(
                        ['prop2'],
                        incorrectInputMessage: 'Property - {property}, type - {type}.',
                    ),
                ],
                ['property' => ['Property - property, type - int.']],
            ],
            'object' => [
                $class,
                [new FilledAtLeast(['prop2'])],
                ['' => ['At least 1 property from this list must be filled for value: "prop2".']],
            ],
            'object, custom min' => [
                $class,
                [new FilledAtLeast(['prop1', 'prop2'], min: 2)],
                ['' => ['At least 2 properties from this list must be filled for value: "prop1", "prop2".']],
            ],
            'array' => [
                $array,
                [new FilledAtLeast(['prop2'])],
                ['' => ['At least 1 property from this list must be filled for value: "prop2".']],
            ],
            'array, custom min' => [
                $array,
                [new FilledAtLeast(['prop1', 'prop2'], min: 2)],
                ['' => ['At least 2 properties from this list must be filled for value: "prop1", "prop2".']],
            ],
            'custom message' => [
                $class,
                [new FilledAtLeast(['prop1', 'prop2'], min: 2, message: 'Custom message.')],
                ['' => ['Custom message.']],
            ],
            'custom message with parameters' => [
                $class,
                [new FilledAtLeast(['prop1', 'prop2'], min: 2, message: 'Properties - {Properties}, min - {min}.')],
                ['' => ['Properties - "Prop1", "Prop2", min - 2.']],
            ],
            'custom message with parameters, property set' => [
                ['data' => $class],
                ['data' => new FilledAtLeast(['prop1', 'prop2'], min: 2, message: 'Properties - {properties}, min - {min}.')],
                ['data' => ['Properties - "prop1", "prop2", min - 2.']],
            ],
            'class property, translated properties' => [
                new AtLeastDto(),
                null,
                ['' => ['At least 1 property from this list must be filled for value: "A", "B", "C".']],
            ],
        ];
    }

    public function testSkipOnError(): void
    {
        $this->testSkipOnErrorInternal(new FilledAtLeast(['prop']), new FilledAtLeast(['prop'], skipOnError: true));
    }

    public function testWhen(): void
    {
        $when = static fn (mixed $value): bool => $value !== null;
        $this->testWhenInternal(new FilledAtLeast(['prop']), new FilledAtLeast(['prop'], when: $when));
    }

    protected function getDifferentRuleInHandlerItems(): array
    {
        return [FilledAtLeast::class, FilledAtLeastHandler::class];
    }
}
