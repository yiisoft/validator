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
                        'message' => 'Value must be array or iterable.',
                    ],
                    'incorrectInputKeyMessage' => [
                        'message' => 'Every iterable key must have an integer or a string type.',
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
                                'message' => 'The allowed types are integer, float and string.',
                            ],
                            'notANumberMessage' => [
                                'message' => 'Value must be a number.',
                            ],
                            'tooSmallMessage' => [
                                'message' => 'Value must be no less than {min}.',
                                'parameters' => ['min' => null],
                            ],
                            'tooBigMessage' => [
                                'message' => 'Value must be no greater than {max}.',
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
                                'message' => 'The allowed types are integer, float and string.',
                            ],
                            'notANumberMessage' => [
                                'message' => 'Value must be a number.',
                            ],
                            'tooSmallMessage' => [
                                'message' => 'Value must be no less than {min}.',
                                'parameters' => ['min' => null],
                            ],
                            'tooBigMessage' => [
                                'message' => 'Value must be no greater than {max}.',
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
                        'message' => 'Value must be array or iterable.',
                    ],
                    'incorrectInputKeyMessage' => [
                        'message' => 'Every iterable key must have an integer or a string type.',
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
                                'message' => 'The allowed types are integer, float and string.',
                            ],
                            'notANumberMessage' => [
                                'message' => 'Value must be a number.',
                            ],
                            'tooSmallMessage' => [
                                'message' => 'Value must be no less than {min}.',
                                'parameters' => ['min' => null],
                            ],
                            'tooBigMessage' => [
                                'message' => 'Value must be no greater than {max}.',
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
            'incorrect input key' => [
                ['attribute' => $getGeneratorWithIncorrectKey()],
                ['attribute' => new Each([new Number(max: 13)])],
                ['attribute' => ['Every iterable key must have an integer or a string type.']],
            ],
            [
                [10, 20, 30],
                [new Each([new Number(max: 13)])],
                [
                    '1' => ['Value must be no greater than 13.'],
                    '2' => ['Value must be no greater than 13.'],
                ],
            ],
            'custom error' => [
                [10, 20, 30],
                [new Each([new Number(max: 13, tooBigMessage: 'Custom error.')])],
                [
                    '1' => ['Custom error.'],
                    '2' => ['Custom error.'],
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
