<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use Yiisoft\Validator\Rule\Composite;
use Yiisoft\Validator\Rule\CompositeHandler;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Tests\Rule\Base\DifferentRuleInHandlerTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\RuleTestCase;
use Yiisoft\Validator\Tests\Rule\Base\SerializableRuleTestTrait;

final class CompositeTest extends RuleTestCase
{
    use DifferentRuleInHandlerTestTrait;
    use SerializableRuleTestTrait;

    public function testGetName(): void
    {
        $rule = new Composite([]);
        $this->assertSame('composite', $rule->getName());
    }

    public function dataOptions(): array
    {
        return [
            [
                new Composite([
                    new Number(max: 13, integerPattern: '/1/', numberPattern: '/1/'),
                    new Number(max: 14, integerPattern: '/2/', numberPattern: '/2/'),
                ]),
                [
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
                20,
                [
                    new Composite(
                        rules: [new Number(max: 13)],
                        when: fn () => false,
                    ),
                ],
            ],
            [
                20,
                [
                    new Composite(
                        rules: [new Number(max: 13)],
                        skipOnError: true,
                    ),
                ],
            ],
            [
                null,
                [
                    new Composite(
                        rules: [new Number(max: 13)],
                        skipOnEmpty: true,
                    ),
                ],
            ],
        ];
    }

    public function dataValidationFailed(): array
    {
        return [
            [
                20,
                [
                    new Composite(
                        rules: [new Number(max: 13), new Number(min: 21)],
                        when: fn () => true,
                    ),
                ],
                [
                    '' => [
                        'Value must be no greater than 13.',
                        'Value must be no less than 21.',
                    ],
                ],
            ],
            'custom error' => [
                20,
                [
                    new Composite(
                        rules: [new Number(max: 13, tooBigMessage: 'Custom error')],
                        when: fn () => true,
                    ),
                ],
                ['' => ['Custom error']],
            ],
        ];
    }

    protected function getDifferentRuleInHandlerItems(): array
    {
        return [Composite::class, CompositeHandler::class];
    }
}
