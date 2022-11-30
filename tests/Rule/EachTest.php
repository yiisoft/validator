<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use Generator;
use Yiisoft\Validator\Rule\Each;
use Yiisoft\Validator\Rule\EachHandler;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Tests\Rule\Base\DifferentRuleInHandlerTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\RuleTestCase;
use Yiisoft\Validator\Tests\Rule\Base\SerializableRuleTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\SkipOnErrorTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\WhenTestTrait;
use Yiisoft\Validator\Tests\Support\Rule\RuleWithoutOptions;

final class EachTest extends RuleTestCase
{
    use DifferentRuleInHandlerTestTrait;
    use SerializableRuleTestTrait;
    use SkipOnErrorTestTrait;
    use WhenTestTrait;

    public function testGetName(): void
    {
        $rule = new Each();
        $this->assertSame('each', $rule->getName());
    }

    public function dataOptions(): array
    {
        return [
            [
                new Each([
                    new Number(max: 13, integerPattern: '/1/', numberPattern: '/1/'),
                    new Number(max: 14, integerPattern: '/2/', numberPattern: '/2/'),
                ]),
                [
                    'incorrectInputMessage' => [
                        'template' => 'Value must be array or iterable.',
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
                            'number',
                            'asInteger' => false,
                            'min' => null,
                            'max' => 13,
                            'incorrectInputMessage' => [
                                'template' => 'The allowed types are integer, float and string.',
                                'parameters' => [],
                            ],
                            'notNumberMessage' => [
                                'template' => 'Value must be a number.',
                                'parameters' => [],
                            ],
                            'tooSmallMessage' => [
                                'template' => 'Value must be no less than {min}.',
                                'parameters' => ['min' => null],
                            ],
                            'tooBigMessage' => [
                                'template' => 'Value must be no greater than {max}.',
                                'parameters' => ['max' => 13],
                            ],
                            'skipOnEmpty' => false,
                            'skipOnError' => false,
                            'integerPattern' => '/1/',
                            'numberPattern' => '/1/',
                        ],
                        [
                            'number',
                            'asInteger' => false,
                            'min' => null,
                            'max' => 14,
                            'incorrectInputMessage' => [
                                'template' => 'The allowed types are integer, float and string.',
                                'parameters' => [],
                            ],
                            'notNumberMessage' => [
                                'template' => 'Value must be a number.',
                                'parameters' => [],
                            ],
                            'tooSmallMessage' => [
                                'template' => 'Value must be no less than {min}.',
                                'parameters' => ['min' => null],
                            ],
                            'tooBigMessage' => [
                                'template' => 'Value must be no greater than {max}.',
                                'parameters' => ['max' => 14],
                            ],
                            'skipOnEmpty' => false,
                            'skipOnError' => false,
                            'integerPattern' => '/2/',
                            'numberPattern' => '/2/',
                        ],
                    ],
                ],
            ],
            'rule without options' => [
                new Each([
                    new Number(max: 13, integerPattern: '/1/', numberPattern: '/1/'),
                    new RuleWithoutOptions(),
                ]),
                [
                    'incorrectInputMessage' => [
                        'template' => 'Value must be array or iterable.',
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
                            'number',
                            'asInteger' => false,
                            'min' => null,
                            'max' => 13,
                            'incorrectInputMessage' => [
                                'template' => 'The allowed types are integer, float and string.',
                                'parameters' => [],
                            ],
                            'notNumberMessage' => [
                                'template' => 'Value must be a number.',
                                'parameters' => [],
                            ],
                            'tooSmallMessage' => [
                                'template' => 'Value must be no less than {min}.',
                                'parameters' => ['min' => null],
                            ],
                            'tooBigMessage' => [
                                'template' => 'Value must be no greater than {max}.',
                                'parameters' => ['max' => 13],
                            ],
                            'skipOnEmpty' => false,
                            'skipOnError' => false,
                            'integerPattern' => '/1/',
                            'numberPattern' => '/1/',
                        ],
                        [
                            'test',
                        ],
                    ],
                ],
            ],
        ];
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
            'incorrect input' => [1, [new Each([new Number(max: 13)])], ['' => ['Value must be array or iterable.']]],
            'custom incorrect input message' => [
                1,
                [new Each([new Number(max: 13)], incorrectInputMessage: 'Custom incorrect input message.')],
                ['' => ['Custom incorrect input message.']],
            ],
            'custom incorrect input message with parameters' => [
                1,
                [new Each([new Number(max: 13)], incorrectInputMessage: 'Attribute - {attribute}, type - {type}.')],
                ['' => ['Attribute - , type - int.']],
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
                    '1' => ['Value must be no greater than 13.'],
                    '2' => ['Value must be no greater than 13.'],
                ],
            ],

            'custom message' => [
                [10, 20, 30],
                [new Each([new Number(max: 13, tooBigMessage: 'Custom too big message.')])],
                [
                    '1' => ['Custom too big message.'],
                    '2' => ['Custom too big message.'],
                ],
            ],
            'custom message with parameters' => [
                [10, 20, 30],
                [
                    new Each(
                        [new Number(max: 13, tooBigMessage: 'Max - {max}, value - {value}.')],
                    ),
                ],
                [
                    '1' => ['Max - 13, value - 20.'],
                    '2' => ['Max - 13, value - 30.'],
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
