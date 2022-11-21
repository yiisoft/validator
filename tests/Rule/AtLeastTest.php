<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use Yiisoft\Validator\Rule\AtLeast;
use Yiisoft\Validator\Rule\AtLeastHandler;
use Yiisoft\Validator\Tests\Rule\Base\DifferentRuleInHandlerTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\RuleTestCase;
use Yiisoft\Validator\Tests\Rule\Base\SerializableRuleTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\SkipOnErrorTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\WhenTestTrait;

final class AtLeastTest extends RuleTestCase
{
    use DifferentRuleInHandlerTestTrait;
    use SerializableRuleTestTrait;
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
                    'incorrectInputMessage' => 'Value must be an array or an object.',
                    'message' => [
                        'message' => 'The model is not valid. Must have at least "{min}" filled attributes.',
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
                    'incorrectInputMessage' => 'Value must be an array or an object.',
                    'message' => [
                        'message' => 'The model is not valid. Must have at least "{min}" filled attributes.',
                        'parameters' => ['min' => 2],
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
                ['' => ['Value must be an array or an object.']],
            ],
            'incorrect input, custom error message' => [
                1,
                [new AtLeast(['attr2'], incorrectInputMessage: 'Attribute - {attribute}, type - {type}.')],
                ['' => ['Attribute - , type - int.']],
            ],
            'object' => [
                $class,
                [new AtLeast(['attr2'])],
                ['' => ['The model is not valid. Must have at least "1" filled attributes.']],
            ],
            'object, custom min' => [
                $class,
                [new AtLeast(['attr1', 'attr2'], min: 2)],
                ['' => ['The model is not valid. Must have at least "2" filled attributes.']],
            ],
            'array' => [
                $array,
                [new AtLeast(['attr2'])],
                ['' => ['The model is not valid. Must have at least "1" filled attributes.']],
            ],
            'array, custom min' => [
                $array,
                [new AtLeast(['attr2'], min: 2)],
                ['' => ['The model is not valid. Must have at least "2" filled attributes.']],
            ],
            'custom error message' => [
                $class,
                [new AtLeast(['attr1', 'attr2'], min: 2, message: 'Custom error')],
                ['' => ['Custom error']],
            ],
            'custom error message with parameters' => [
                $class,
                [new AtLeast(['attr1', 'attr2'], min: 2, message: 'Attribute - {attribute}, min - {min}.')],
                ['' => ['Attribute - , min - 2.']],
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
