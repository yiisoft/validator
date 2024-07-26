<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use Generator;
use stdClass;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\Callback;
use Yiisoft\Validator\Rule\Each;
use Yiisoft\Validator\Rule\EachHandler;
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\Rule\StringValue;
use Yiisoft\Validator\Tests\Rule\Base\DifferentRuleInHandlerTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\RuleTestCase;
use Yiisoft\Validator\Tests\Rule\Base\RuleWithOptionsTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\RuleWithProvidedRulesTrait;
use Yiisoft\Validator\Tests\Rule\Base\SkipOnErrorTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\WhenTestTrait;
use Yiisoft\Validator\Tests\Support\Rule\RuleWithoutOptions;
use Yiisoft\Validator\Validator;
use Yiisoft\Validator\ValidationContext;

final class EachTest extends RuleTestCase
{
    use DifferentRuleInHandlerTestTrait;
    use RuleWithOptionsTestTrait;
    use RuleWithProvidedRulesTrait;
    use SkipOnErrorTestTrait;
    use WhenTestTrait;

    public function testGetName(): void
    {
        $rule = new Each();
        $this->assertSame(Each::class, $rule->getName());
    }

    public function dataOptions(): array
    {
        return [
            'basic' => [
                new Each([
                    new Number(max: 13, pattern: '/1/'),
                    new Number(max: 14, pattern: '/2/'),
                ]),
                [
                    'incorrectInputMessage' => [
                        'template' => '{Property} must be array or iterable.',
                        'parameters' => [],
                    ],
                    'incorrectInputKeyMessage' => [
                        'template' => 'Every iterable key must have an integer or a string type.',
                        'parameters' => [],
                    ],
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                    'rules' => [
                        [
                            [
                                Number::class,
                                'min' => null,
                                'max' => 13,
                                'incorrectInputMessage' => [
                                    'template' => 'The allowed types for {property} are integer, float and string.',
                                    'parameters' => [],
                                ],
                                'notNumberMessage' => [
                                    'template' => '{Property} must be a number.',
                                    'parameters' => [],
                                ],
                                'lessThanMinMessage' => [
                                    'template' => '{Property} must be no less than {min}.',
                                    'parameters' => ['min' => null],
                                ],
                                'greaterThanMaxMessage' => [
                                    'template' => '{Property} must be no greater than {max}.',
                                    'parameters' => ['max' => 13],
                                ],
                                'skipOnEmpty' => false,
                                'skipOnError' => false,
                                'pattern' => '/1/',
                            ],
                        ],
                        [
                            [
                                Number::class,
                                'min' => null,
                                'max' => 14,
                                'incorrectInputMessage' => [
                                    'template' => 'The allowed types for {property} are integer, float and string.',
                                    'parameters' => [],
                                ],
                                'notNumberMessage' => [
                                    'template' => '{Property} must be a number.',
                                    'parameters' => [],
                                ],
                                'lessThanMinMessage' => [
                                    'template' => '{Property} must be no less than {min}.',
                                    'parameters' => ['min' => null],
                                ],
                                'greaterThanMaxMessage' => [
                                    'template' => '{Property} must be no greater than {max}.',
                                    'parameters' => ['max' => 14],
                                ],
                                'skipOnEmpty' => false,
                                'skipOnError' => false,
                                'pattern' => '/2/',
                            ],
                        ],
                    ],
                ],
            ],
            'rule without options' => [
                new Each([
                    new Number(max: 13, pattern: '/1/'),
                    new RuleWithoutOptions(),
                ]),
                [
                    'incorrectInputMessage' => [
                        'template' => '{Property} must be array or iterable.',
                        'parameters' => [],
                    ],
                    'incorrectInputKeyMessage' => [
                        'template' => 'Every iterable key must have an integer or a string type.',
                        'parameters' => [],
                    ],
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                    'rules' => [
                        [
                            [
                                Number::class,
                                'min' => null,
                                'max' => 13,
                                'incorrectInputMessage' => [
                                    'template' => 'The allowed types for {property} are integer, float and string.',
                                    'parameters' => [],
                                ],
                                'notNumberMessage' => [
                                    'template' => '{Property} must be a number.',
                                    'parameters' => [],
                                ],
                                'lessThanMinMessage' => [
                                    'template' => '{Property} must be no less than {min}.',
                                    'parameters' => ['min' => null],
                                ],
                                'greaterThanMaxMessage' => [
                                    'template' => '{Property} must be no greater than {max}.',
                                    'parameters' => ['max' => 13],
                                ],
                                'skipOnEmpty' => false,
                                'skipOnError' => false,
                                'pattern' => '/1/',
                            ],
                        ],
                        [
                            [
                                RuleWithoutOptions::class,
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    public function testGetOptionsWithNotRule(): void
    {
        $this->testGetOptionsWithNotRuleInternal(Each::class);
    }

    public function dataValidationPassed(): array
    {
        return [
            'base' => [
                [10, 11],
                [new Each([new Number(max: 20)])],
            ],
            'double-each-with-arrays' => [
                [
                    'items' => [
                        ['variantId' => 123, 'quantity' => 1],
                    ],
                ],
                [
                    'items' => [
                        new Each([
                            new Callback(callback: function (array $item) {
                                return new Result();
                            }),
                        ]),
                        new Each([
                            new Callback(callback: function (array $item) {
                                return new Result();
                            }),
                        ]),
                    ],
                ],
            ],
        ];
    }

    public function dataValidationFailed(): array
    {
        $getGeneratorWithIncorrectKey = static function (): Generator {
            yield false => 0;
        };

        return [
            'incorrect input' => [1, [new Each([new Number(max: 13)])], ['' => ['Value must be array or iterable.']]],
            'custom incorrect input message' => [
                1,
                [new Each([new Number(max: 13)], incorrectInputMessage: 'Custom incorrect input message.')],
                ['' => ['Custom incorrect input message.']],
            ],
            'custom incorrect input message, plain Each rule' => [
                1,
                new Each([new Number(max: 13)], incorrectInputMessage: 'Custom incorrect input message.'),
                ['' => ['Custom incorrect input message.']],
            ],
            'custom incorrect input message with parameters' => [
                1,
                [new Each([new Number(max: 13)], incorrectInputMessage: 'Property - {property}, type - {type}.')],
                ['' => ['Property - value, type - int.']],
            ],
            'custom incorrect input message with parameters, property set' => [
                ['data' => 1],
                [
                    'data' => new Each(
                        [new Number(max: 13)],
                        incorrectInputMessage: 'Property - {Property}, type - {type}.',
                    ),
                ],
                ['data' => ['Property - Data, type - int.']],
            ],

            'incorrect input key' => [
                ['property' => $getGeneratorWithIncorrectKey()],
                ['property' => new Each([new Number(max: 13)])],
                ['property' => ['Every iterable key must have an integer or a string type.']],
            ],
            'custom incorrect input key message' => [
                ['property' => $getGeneratorWithIncorrectKey()],
                [
                    'property' => new Each(
                        [new Number(max: 13)],
                        incorrectInputKeyMessage: 'Custom incorrect input key message.',
                    ),
                ],
                ['property' => ['Custom incorrect input key message.']],
            ],
            'custom incorrect input key message with parameters' => [
                ['property' => $getGeneratorWithIncorrectKey()],
                [
                    'property' => new Each(
                        [new Number(max: 13)],
                        incorrectInputKeyMessage: 'Property - {property}, type - {type}.',
                    ),
                ],
                ['property' => ['Property - property, type - Generator.']],
            ],

            [
                [10, 20, 30],
                [new Each([new Number(max: 13)])],
                [
                    '1' => ['Value must be no greater than 13.'],
                    '2' => ['Value must be no greater than 13.'],
                ],
            ],

            'single rule' => [
                [10, 20, 30],
                [new Each(new Number(max: 13))],
                [
                    '1' => ['Value must be no greater than 13.'],
                    '2' => ['Value must be no greater than 13.'],
                ],
            ],
            'single callable rule' => [
                [10, 20],
                [new Each(static fn (): Result => (new Result())->addError('error'))],
                [
                    0 => ['error'],
                    1 => ['error'],
                ],
            ],
            'rules array with callable' => [
                [10, 20],
                [new Each([static fn (): Result => (new Result())->addError('error')])],
                [
                    0 => ['error'],
                    1 => ['error'],
                ],
            ],

            'custom message' => [
                [10, 20, 30],
                [new Each([new Number(max: 13, greaterThanMaxMessage: 'Custom too big message.')])],
                [
                    '1' => ['Custom too big message.'],
                    '2' => ['Custom too big message.'],
                ],
            ],
            'custom message with parameters' => [
                [10, 20, 30],
                [
                    new Each(
                        [new Number(max: 13, greaterThanMaxMessage: 'Max - {max}, value - {value}.')],
                    ),
                ],
                [
                    '1' => ['Max - 13, value - 20.'],
                    '2' => ['Max - 13, value - 30.'],
                ],
            ],
            'validate arrays' => [
                [['name' => 'Mi', 'age' => 31], ['name' => 'SuHo', 'age' => 17]],
                new Each([
                    'name' => [new Required(), new Length(min: 3)],
                    'age' => new Number(min: 18),
                ]),
                [
                    '0.name' => ['Name must contain at least 3 characters.'],
                    '1.age' => ['Age must be no less than 18.'],
                ],
            ],
            'validate with labels' => [
                ['a' => [1, 2], 'b' => [new stdClass(), 0, 'test']],
                [
                    'a' => new StringValue(),
                    'b' => new Each(new StringValue()),
                ],
                [
                    'a' => ['A must be a string.'],
                    'b.0' => ['B must be a string.'],
                    'b.1' => ['B must be a string.'],
                ],
            ],
        ];
    }

    public function testSkipOnError(): void
    {
        $this->testSkipOnErrorInternal(new Each(), new Each(skipOnError: true));
    }

    public function testWhen(): void
    {
        $when = static fn (mixed $value): bool => $value !== null;
        $this->testWhenInternal(new Each(), new Each(when: $when));
    }

    protected function getDifferentRuleInHandlerItems(): array
    {
        return [Each::class, EachHandler::class];
    }

    public static function dataContextEachKey(): array
    {
        return [
            [
                [10, 20, 30],
                [0, 1, 2],
            ],
            [
                ['key1' => 10, 'key2' => '2 test', 'key3' => 30],
                ['key1', 'key2', 'key3'],
            ],
            [
                [3 => 10, 'key2' => '2 test', 'key3' => 30],
                [3, 'key2', 'key3'],
            ],
        ];
    }

    /**
     * @dataProvider dataContextEachKey
     */
    public function testContextEachKey($data, $keys): void
    {
        $indexes = [];
        $rules = [
            new Each(
                new Callback(
                    function (mixed $value, object $rule, ValidationContext $context) use (&$indexes) {
                        $indexes[] = $context->getParameter(Each::PARAMETER_EACH_KEY);
                        return new Result();
                    }
                ),
            ),
        ];

        $result = (new Validator())->validate($data, $rules);

        $this->assertTrue($result->isValid());
        $this->assertSame($keys, $indexes);
    }

    public function testNestedContextEachKey(): void
    {
        $indexes = [];
        $callback = new Callback(
            function (mixed $value, object $rule, ValidationContext $context) use (&$indexes) {
                $indexes[] = $context->getParameter(Each::PARAMETER_EACH_KEY);
                return new Result();
            }
        );

        (new Validator())->validate(
            [
                'a' => ['x' => 1, 'y' => 2],
                'b' => ['z' => 3],
            ],
            new Each([
                new Each($callback),
                $callback,
            ]),
        );

        $this->assertSame(['x', 'y', 'a', 'z', 'b'], $indexes);
    }
}
