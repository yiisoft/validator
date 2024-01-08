<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use Generator;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\Each;
use Yiisoft\Validator\Rule\EachHandler;
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\Tests\Rule\Base\DifferentRuleInHandlerTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\RuleTestCase;
use Yiisoft\Validator\Tests\Rule\Base\RuleWithOptionsTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\RuleWithProvidedRulesTrait;
use Yiisoft\Validator\Tests\Rule\Base\SkipOnErrorTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\WhenTestTrait;
use Yiisoft\Validator\Tests\Support\Rule\RuleWithoutOptions;

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
            [
                new Each([
                    new Number(max: 13, pattern: '/1/'),
                    new Number(max: 14, pattern: '/2/'),
                ]),
                [
                    'incorrectInputMessage' => [
                        'template' => '{attribute} must be array or iterable.',
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
                                    'template' => 'The allowed types are integer, float and string.',
                                    'parameters' => [],
                                ],
                                'notNumberMessage' => [
                                    'template' => '{attribute} must be a number.',
                                    'parameters' => [],
                                ],
                                'lessThanMinMessage' => [
                                    'template' => '{attribute} must be no less than {min}.',
                                    'parameters' => ['min' => null],
                                ],
                                'greaterThanMaxMessage' => [
                                    'template' => '{attribute} must be no greater than {max}.',
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
                                    'template' => 'The allowed types are integer, float and string.',
                                    'parameters' => [],
                                ],
                                'notNumberMessage' => [
                                    'template' => '{attribute} must be a number.',
                                    'parameters' => [],
                                ],
                                'lessThanMinMessage' => [
                                    'template' => '{attribute} must be no less than {min}.',
                                    'parameters' => ['min' => null],
                                ],
                                'greaterThanMaxMessage' => [
                                    'template' => '{attribute} must be no greater than {max}.',
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
                        'template' => '{attribute} must be array or iterable.',
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
                                    'template' => 'The allowed types are integer, float and string.',
                                    'parameters' => [],
                                ],
                                'notNumberMessage' => [
                                    'template' => '{attribute} must be a number.',
                                    'parameters' => [],
                                ],
                                'lessThanMinMessage' => [
                                    'template' => '{attribute} must be no less than {min}.',
                                    'parameters' => ['min' => null],
                                ],
                                'greaterThanMaxMessage' => [
                                    'template' => '{attribute} must be no greater than {max}.',
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
            [
                [10, 11],
                [new Each([new Number(max: 20)])],
            ],
        ];
    }

    public function dataValidationFailed(): array
    {
        $getGeneratorWithIncorrectKey = static function (): Generator {
            yield false => 0;
        };

        return [
            'incorrect input' => [1, [new Each([new Number(max: 13)])], ['' => ['The value must be array or iterable.']]],
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
                [new Each([new Number(max: 13)], incorrectInputMessage: 'Attribute - {attribute}, type - {type}.')],
                ['' => ['Attribute - The value, type - int.']],
            ],
            'custom incorrect input message with parameters, attribute set' => [
                ['data' => 1],
                [
                    'data' => new Each(
                        [new Number(max: 13)],
                        incorrectInputMessage: 'Attribute - {attribute}, type - {type}.',
                    ),
                ],
                ['data' => ['Attribute - data, type - int.']],
            ],

            'incorrect input key' => [
                ['attribute' => $getGeneratorWithIncorrectKey()],
                ['attribute' => new Each([new Number(max: 13)])],
                ['attribute' => ['Every iterable key must have an integer or a string type.']],
            ],
            'custom incorrect input key message' => [
                ['attribute' => $getGeneratorWithIncorrectKey()],
                [
                    'attribute' => new Each(
                        [new Number(max: 13)],
                        incorrectInputKeyMessage: 'Custom incorrect input key message.',
                    ),
                ],
                ['attribute' => ['Custom incorrect input key message.']],
            ],
            'custom incorrect input key message with parameters' => [
                ['attribute' => $getGeneratorWithIncorrectKey()],
                [
                    'attribute' => new Each(
                        [new Number(max: 13)],
                        incorrectInputKeyMessage: 'Attribute - {attribute}, type - {type}.',
                    ),
                ],
                ['attribute' => ['Attribute - attribute, type - Generator.']],
            ],

            [
                [10, 20, 30],
                [new Each([new Number(max: 13)])],
                [
                    '1' => ['The value must be no greater than 13.'],
                    '2' => ['The value must be no greater than 13.'],
                ],
            ],

            'single rule' => [
                [10, 20, 30],
                [new Each(new Number(max: 13))],
                [
                    '1' => ['The value must be no greater than 13.'],
                    '2' => ['The value must be no greater than 13.'],
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
                    '0.name' => ['name must contain at least 3 characters.'],
                    '1.age' => ['age must be no less than 18.'],
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
}
