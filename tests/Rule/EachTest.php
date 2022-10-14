<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use Yiisoft\Validator\Rule\Each;
use Yiisoft\Validator\Rule\EachHandler;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Tests\Rule\Base\DifferentRuleInHandlerTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\RuleTestCase;
use Yiisoft\Validator\Tests\Rule\Base\SerializableRuleTestTrait;

final class EachTest extends RuleTestCase
{
    use DifferentRuleInHandlerTestTrait;
    use SerializableRuleTestTrait;

    public function testGetName(): void
    {
        $rule = new Each([]);
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
                    'message' => [
                        'message' => '{error} {value} given.',
                    ],
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                    'rules' => [
                        [
                            'number',
                            'asInteger' => false,
                            'min' => null,
                            'max' => 13,
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
        return [
            [
                [10, 20, 30],
                [new Each([new Number(max: 13)])],
                [
                    '1' => ['Value must be no greater than 13. 20 given.'],
                    '2' => ['Value must be no greater than 13. 30 given.'],
                ],
            ],
            'custom error' => [
                [10, 20, 30],
                [new Each([new Number(max: 13, tooBigMessage: 'Custom error.')])],
                [
                    '1' => ['Custom error. 20 given.'],
                    '2' => ['Custom error. 30 given.'],
                ],
            ],
        ];
    }

    protected function getDifferentRuleInHandlerItems(): array
    {
        return [Each::class, EachHandler::class];
    }
}
