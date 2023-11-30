<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use Yiisoft\Validator\EmptyCondition\NeverEmpty;
use Yiisoft\Validator\Rule\OneOf;
use Yiisoft\Validator\Rule\OneOfHandler;
use Yiisoft\Validator\Tests\Rule\Base\DifferentRuleInHandlerTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\RuleTestCase;
use Yiisoft\Validator\Tests\Rule\Base\RuleWithOptionsTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\SkipOnErrorTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\WhenTestTrait;
use Yiisoft\Validator\Tests\Support\Data\OneOfDto;

final class OneOfTest extends RuleTestCase
{
    use DifferentRuleInHandlerTestTrait;
    use RuleWithOptionsTestTrait;
    use SkipOnErrorTestTrait;
    use WhenTestTrait;

    public function testGetName(): void
    {
        $rule = new OneOf([]);
        $this->assertSame(OneOf::class, $rule->getName());
    }

    public function dataOptions(): array
    {
        return [
            [
                new OneOf(['attr1', 'attr2']),
                [
                    'attributes' => [
                        'attr1',
                        'attr2',
                    ],
                    'incorrectInputMessage' => [
                        'template' => 'The value must be an array or an object.',
                        'parameters' => [],
                    ],
                    'message' => [
                        'template' => 'Exactly 1 attribute from this list must be filled: {attributes}.',
                        'parameters' => [],
                    ],
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            'callable skip on empty' => [
                new OneOf(['attr1', 'attr2'], skipOnEmpty: new NeverEmpty()),
                [
                    'attributes' => [
                        'attr1',
                        'attr2',
                    ],
                    'incorrectInputMessage' => [
                        'template' => 'The value must be an array or an object.',
                        'parameters' => [],
                    ],
                    'message' => [
                        'template' => 'Exactly 1 attribute from this list must be filled: {attributes}.',
                        'parameters' => [],
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
                [new OneOf(['attr1', 'attr2'])],
            ],
            [
                new class () {
                    public $attr1 = null;
                    public $attr2 = 1;
                },
                [new OneOf(['attr1', 'attr2'])],
            ],
            [
                new class () {
                    private int $attr1 = 1;
                    private $attr2 = null;
                },
                [new OneOf(['attr1', 'attr2'])],
            ],
            [
                ['attr1' => 1, 'attr2' => null],
                [new OneOf(['attr1', 'attr2'])],
            ],
            [
                ['attr1' => null, 'attr2' => 1],
                [new OneOf(['attr1', 'attr2'])],
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
                ['obj' => new OneOf(['attr1', 'attr2'])],
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
                ['obj' => new OneOf(['attr1', 'attr2'])],
            ],
            [
                ['obj' => ['attr1' => 1, 'attr2' => null]],
                ['obj' => new OneOf(['attr1', 'attr2'])],
            ],
            [
                ['obj' => ['attr1' => null, 'attr2' => 1]],
                ['obj' => new OneOf(['attr1', 'attr2'])],
            ],
            'class attribute, translation' => [
                new OneOfDto(1),
            ],
        ];
    }

    public function dataValidationFailed(): array
    {
        $object = new class () {
            public $attr1 = null;
            public $attr2 = null;
        };
        $array = ['attr1' => null, 'attr2' => null];

        return [
            'incorrect input' => [
                1,
                [new OneOf(['attr1', 'attr2'])],
                ['' => ['The value must be an array or an object.']],
            ],
            'custom incorrect input message' => [
                1,
                [new OneOf(['attr1', 'attr2'], incorrectInputMessage: 'Custom incorrect input message.')],
                ['' => ['Custom incorrect input message.']],
            ],
            'custom incorrect input message with parameters' => [
                1,
                [new OneOf(['attr1', 'attr2'], incorrectInputMessage: 'Attribute - {attribute}, type - {type}.')],
                ['' => ['Attribute - , type - int.']],
            ],
            'custom incorrect input message with parameters, attribute set' => [
                ['attribute' => 1],
                [
                    'attribute' => new OneOf(
                        ['attr1', 'attr2'],
                        incorrectInputMessage: 'Attribute - {attribute}, type - {type}.',
                    ),
                ],
                ['attribute' => ['Attribute - attribute, type - int.']],
            ],
            'object' => [
                $object,
                [new OneOf(['attr1', 'attr2'])],
                ['' => ['Exactly 1 attribute from this list must be filled: "attr1", "attr2".']],
            ],
            'array' => [
                $array,
                [new OneOf(['attr1', 'attr2'])],
                ['' => ['Exactly 1 attribute from this list must be filled: "attr1", "attr2".']],
            ],
            'more than 1 attribute is filled' => [
                ['attr1' => 1, 'attr2' => 2],
                [new OneOf(['attr1', 'attr2'])],
                ['' => ['Exactly 1 attribute from this list must be filled: "attr1", "attr2".']],
            ],
            'custom message' => [
                $object,
                [new OneOf(['attr1', 'attr2'], message: 'Custom message.')],
                ['' => ['Custom message.']],
            ],
            'custom message with parameters' => [
                $object,
                [new OneOf(['attr1', 'attr2'], message: 'Attributes - {attributes}.')],
                ['' => ['Attributes - "attr1", "attr2".']],
            ],
            'custom message with parameters, attribute set' => [
                ['data' => $object],
                ['data' => new OneOf(['attr1', 'attr2'], message: 'Attributes - {attributes}.')],
                ['data' => ['Attributes - "attr1", "attr2".']],
            ],
            'class attribute' => [
                new OneOfDto(),
                null,
                ['' => ['Exactly 1 attribute from this list must be filled: "A", "B", "C".']],
            ],
        ];
    }

    public function testSkipOnError(): void
    {
        $this->testSkipOnErrorInternal(new OneOf([]), new OneOf([], skipOnError: true));
    }

    public function testWhen(): void
    {
        $when = static fn (mixed $value): bool => $value !== null;
        $this->testWhenInternal(new OneOf([]), new OneOf([], when: $when));
    }

    protected function getDifferentRuleInHandlerItems(): array
    {
        return [OneOf::class, OneOfHandler::class];
    }
}
