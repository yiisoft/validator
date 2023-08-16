<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use Yiisoft\Validator\EmptyCondition\NeverEmpty;
use Yiisoft\Validator\Rule\AtLeast;
use Yiisoft\Validator\Rule\AtLeastHandler;
use Yiisoft\Validator\Tests\Rule\Base\DifferentRuleInHandlerTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\RuleTestCase;
use Yiisoft\Validator\Tests\Rule\Base\RuleWithOptionsTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\SkipOnErrorTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\WhenTestTrait;
use Yiisoft\Validator\Tests\Support\Data\AtLeastDto;

final class AtLeastTest extends RuleTestCase
{
    use DifferentRuleInHandlerTestTrait;
    use RuleWithOptionsTestTrait;
    use SkipOnErrorTestTrait;
    use WhenTestTrait;

    public function testGetName(): void
    {
        $rule = new AtLeast([]);
        $this->assertSame('atLeast', $rule->getName());
    }

    public function dataOptions(): array
    {
        return [
            [
                new AtLeast(['attr1', 'attr2']),
                [
                    'attributes' => [
                        'attr1',
                        'attr2',
                    ],
                    'min' => 1,
                    'incorrectInputMessage' => [
                        'template' => 'The value must be an array or an object.',
                        'parameters' => [],
                    ],
                    'message' => [
                        'template' => 'The data must have at least "{min}" filled attributes.',
                        'parameters' => ['min' => 1],
                    ],
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                new AtLeast(['attr1', 'attr2'], min: 2),
                [
                    'attributes' => [
                        'attr1',
                        'attr2',
                    ],
                    'min' => 2,
                    'incorrectInputMessage' => [
                        'template' => 'The value must be an array or an object.',
                        'parameters' => [],
                    ],
                    'message' => [
                        'template' => 'The data must have at least "{min}" filled attributes.',
                        'parameters' => ['min' => 2],
                    ],
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            'callable skip on empty' => [
                new AtLeast(['attr1', 'attr2'], skipOnEmpty: new NeverEmpty()),
                [
                    'attributes' => [
                        'attr1',
                        'attr2',
                    ],
                    'min' => 1,
                    'incorrectInputMessage' => [
                        'template' => 'The value must be an array or an object.',
                        'parameters' => [],
                    ],
                    'message' => [
                        'template' => 'The data must have at least "{min}" filled attributes.',
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
                    public $attr1 = 1;
                    public $attr2 = null;
                },
                [new AtLeast(['attr1', 'attr2'])],
            ],
            [
                new class () {
                    public $attr1 = null;
                    public $attr2 = 1;
                },
                [new AtLeast(['attr2'])],
            ],
            [
                new class () {
                    private int $attr1 = 1;
                    private $attr2 = null;
                },
                [new AtLeast(['attr1', 'attr2'])],
            ],
            [
                ['attr1' => 1, 'attr2' => null],
                [new AtLeast(['attr1', 'attr2'])],
            ],
            [
                ['attr1' => null, 'attr2' => 1],
                [new AtLeast(['attr2'])],
            ],
            [
                new class () {
                    public $obj;

                    public function __construct()
                    {
                        $this->obj = new class () {
                            public $attr1 = 1;
                            public $attr2 = null;
                        };
                    }
                },
                ['obj' => new AtLeast(['attr1', 'attr2'])],
            ],
            [
                new class () {
                    public $obj;

                    public function __construct()
                    {
                        $this->obj = new class () {
                            public $attr1 = null;
                            public $attr2 = 1;
                        };
                    }
                },
                ['obj' => new AtLeast(['attr2'])],
            ],
            [
                ['obj' => ['attr1' => 1, 'attr2' => null]],
                ['obj' => new AtLeast(['attr1', 'attr2'])],
            ],
            [
                ['obj' => ['attr1' => null, 'attr2' => 1]],
                ['obj' => new AtLeast(['attr2'])],
            ],
            'more than "min" attributes are filled' => [
                ['attr1' => 1, 'attr2' => 2],
                [new AtLeast(['attr1', 'attr2'])],
            ],
            'class attribute' => [
                new AtLeastDto(1),
            ],
        ];
    }

    public function dataValidationFailed(): array
    {
        $class = new class () {
            public $attr1 = 1;
            public $attr2 = null;
        };
        $array = ['attr1' => 1, 'attr2' => null];

        return [
            'incorrect input' => [
                1,
                [new AtLeast(['attr2'])],
                ['' => ['The value must be an array or an object.']],
            ],
            'custom incorrect input message' => [
                1,
                [new AtLeast(['attr2'], incorrectInputMessage: 'Custom incorrect input message.')],
                ['' => ['Custom incorrect input message.']],
            ],
            'custom incorrect input message with parameters' => [
                1,
                [new AtLeast(['attr2'], incorrectInputMessage: 'Attribute - {attribute}, type - {type}.')],
                ['' => ['Attribute - , type - int.']],
            ],
            'custom incorrect input message with parameters, attribute set' => [
                ['attribute' => 1],
                [
                    'attribute' => new AtLeast(
                        ['attr2'],
                        incorrectInputMessage: 'Attribute - {attribute}, type - {type}.',
                    ),
                ],
                ['attribute' => ['Attribute - attribute, type - int.']],
            ],
            'object' => [
                $class,
                [new AtLeast(['attr2'])],
                ['' => ['The data must have at least "1" filled attributes.']],
            ],
            'object, custom min' => [
                $class,
                [new AtLeast(['attr1', 'attr2'], min: 2)],
                ['' => ['The data must have at least "2" filled attributes.']],
            ],
            'array' => [
                $array,
                [new AtLeast(['attr2'])],
                ['' => ['The data must have at least "1" filled attributes.']],
            ],
            'array, custom min' => [
                $array,
                [new AtLeast(['attr2'], min: 2)],
                ['' => ['The data must have at least "2" filled attributes.']],
            ],
            'custom message' => [
                $class,
                [new AtLeast(['attr1', 'attr2'], min: 2, message: 'Custom message.')],
                ['' => ['Custom message.']],
            ],
            'custom message with parameters' => [
                $class,
                [new AtLeast(['attr1', 'attr2'], min: 2, message: 'Attribute - {attribute}, min - {min}.')],
                ['' => ['Attribute - , min - 2.']],
            ],
            'custom message with parameters, attribute set' => [
                ['data' => $class],
                ['data' => new AtLeast(['attr1', 'attr2'], min: 2, message: 'Attribute - {attribute}, min - {min}.')],
                ['data' => ['Attribute - data, min - 2.']],
            ],
            'class attribute' => [
                new AtLeastDto(),
                null,
                ['' => ['The data must have at least "1" filled attributes.']],
            ],
        ];
    }

    public function testSkipOnError(): void
    {
        $this->testSkipOnErrorInternal(new AtLeast([]), new AtLeast([], skipOnError: true));
    }

    public function testWhen(): void
    {
        $when = static fn (mixed $value): bool => $value !== null;
        $this->testWhenInternal(new AtLeast([]), new AtLeast([], when: $when));
    }

    protected function getDifferentRuleInHandlerItems(): array
    {
        return [AtLeast::class, AtLeastHandler::class];
    }
}
