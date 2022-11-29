<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use InvalidArgumentException;
use Yiisoft\Validator\Rule\Composite;
use Yiisoft\Validator\Rule\CompositeHandler;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Tests\Rule\Base\DifferentRuleInHandlerTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\RuleTestCase;
use Yiisoft\Validator\Tests\Rule\Base\SerializableRuleTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\SkipOnErrorTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\WhenTestTrait;
use Yiisoft\Validator\Tests\Support\Rule\RuleWithoutOptions;

final class CompositeTest extends RuleTestCase
{
    use DifferentRuleInHandlerTestTrait;
    use SerializableRuleTestTrait;
    use SkipOnErrorTestTrait;
    use WhenTestTrait;

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
                            'incorrectInputMessage' => [
                                'template' => 'The allowed types are integer, float and string.',
                                'parameters' => [],
                            ],
                            'notANumberMessage' => [
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
                            'notANumberMessage' => [
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
                new Composite([
                    new Number(max: 13, integerPattern: '/1/', numberPattern: '/1/'),
                    new RuleWithoutOptions(),
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
                            'incorrectInputMessage' => [
                                'template' => 'The allowed types are integer, float and string.',
                                'parameters' => [],
                            ],
                            'notANumberMessage' => [
                                'template' => 'Value must be a number.',
                                'parameters' => [],
                            ],
                            'tooSmallMessage' => [
                                'template' => 'Value must be no less than {min}.',
                                'parameters' => [
                                    'min' => null,
                                ],
                            ],
                            'tooBigMessage' => [
                                'template' => 'Value must be no greater than {max}.',
                                'parameters' => [
                                    'max' => 13,
                                ],
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

    public function testOptionsWithNotRule(): void
    {
        $rule = new Composite([
            new Number(max: 13, integerPattern: '/1/', numberPattern: '/1/'),
            new class () {
            },
        ]);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('A rule must be an instance of RuleInterface.');
        $rule->getOptions();
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

    public function testSkipOnError(): void
    {
        $this->testSkipOnErrorInternal(new Composite([]), new Composite([], skipOnError: true));
    }

    public function testWhen(): void
    {
        $when = static fn (mixed $value): bool => $value !== null;
        $this->testWhenInternal(new Composite([]), new Composite([], when: $when));
    }

    protected function getDifferentRuleInHandlerItems(): array
    {
        return [Composite::class, CompositeHandler::class];
    }
}
