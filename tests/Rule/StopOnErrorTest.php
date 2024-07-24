<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\Rule\StopOnError;
use Yiisoft\Validator\Rule\StopOnErrorHandler;
use Yiisoft\Validator\Tests\Rule\Base\DifferentRuleInHandlerTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\RuleTestCase;
use Yiisoft\Validator\Tests\Rule\Base\RuleWithOptionsTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\RuleWithProvidedRulesTrait;
use Yiisoft\Validator\Tests\Rule\Base\WhenTestTrait;

final class StopOnErrorTest extends RuleTestCase
{
    use DifferentRuleInHandlerTestTrait;
    use RuleWithOptionsTestTrait;
    use RuleWithProvidedRulesTrait;
    use WhenTestTrait;

    public function testGetName(): void
    {
        $rule = new StopOnError([new Length(min: 10)]);
        $this->assertSame(StopOnError::class, $rule->getName());
    }

    public function dataOptions(): array
    {
        return [
            [
                new StopOnError([new Length(min: 10)]),
                [
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                    'rules' => [
                        [
                            Length::class,
                            'min' => 10,
                            'max' => null,
                            'exactly' => null,
                            'lessThanMinMessage' => [
                                'template' => '{Property} must contain at least {min, number} {min, plural, ' .
                                    'one{character} other{characters}}.',
                                'parameters' => [
                                    'min' => 10,
                                ],
                            ],
                            'greaterThanMaxMessage' => [
                                'template' => '{Property} must contain at most {max, number} {max, plural, ' .
                                    'one{character} other{characters}}.',
                                'parameters' => [
                                    'max' => null,
                                ],
                            ],
                            'notExactlyMessage' => [
                                'template' => '{Property} must contain exactly {exactly, number} {exactly, plural, ' .
                                    'one{character} other{characters}}.',
                                'parameters' => [
                                    'exactly' => null,
                                ],
                            ],
                            'incorrectInputMessage' => [
                                'template' => '{Property} must be a string.',
                                'parameters' => [],
                            ],
                            'encoding' => 'UTF-8',
                            'skipOnEmpty' => false,
                            'skipOnError' => false,
                        ],
                    ],
                ],
            ],
        ];
    }

    public function testGetOptionsWithNotRule(): void
    {
        $this->testGetOptionsWithNotRuleInternal(StopOnError::class);
    }

    public function dataValidationPassed(): array
    {
        return [
            'at least one succeed property' => [
                'hello',
                [
                    new StopOnError([
                        new Length(min: 1),
                        new Length(max: 10),
                    ]),
                ],
            ],
        ];
    }

    public function dataValidationFailed(): array
    {
        return [
            'basic' => [
                'hello',
                [
                    new StopOnError([
                        new Length(min: 10),
                        new Length(max: 1),
                    ]),
                ],
                ['' => ['Value must contain at least 10 characters.']],
            ],
            'basic, different order' => [
                'hello',
                [
                    new StopOnError([
                        new Length(max: 1),
                        new Length(min: 10),
                    ]),
                ],
                ['' => ['Value must contain at most 1 character.']],
            ],
            'basic, plain StopOnError rule' => [
                'hello',
                new StopOnError([
                    new Length(min: 10),
                    new Length(max: 1),
                ]),
                ['' => ['Value must contain at least 10 characters.']],
            ],
            'combined with other top level rules' => [
                'hello',
                [
                    new Number(),
                    new StopOnError([
                        new Length(max: 1),
                        new Length(min: 10),
                    ]),
                    new Length(min: 7),
                ],
                [
                    '' => [
                        'Value must be a number.',
                        'Value must contain at most 1 character.',
                        'Value must contain at least 7 characters.',
                    ],
                ],
            ],
            'combined with other top level rules, skipOnError: true' => [
                'hello',
                [
                    new Number(),
                    new StopOnError(
                        [
                            new Length(max: 1),
                            new Length(min: 10),
                        ],
                        skipOnError: true,
                    ),
                    new Length(min: 7),
                ],
                [
                    '' => [
                        'Value must be a number.',
                        'Value must contain at least 7 characters.',
                    ],
                ],
            ],
            'attributes, multiple StopOnError rules combined with other top level rules' => [
                [],
                [
                    'a' => new Required(),
                    'b' => new StopOnError([
                        new Required(),
                        new Number(min: 7),
                    ]),
                    'c' => new StopOnError([
                        new Required(),
                        new Number(min: 42),
                    ]),
                    'd' => new Required(),
                ],
                [
                    'a' => ['A not passed.'],
                    'b' => ['B not passed.'],
                    'c' => ['C not passed.'],
                    'd' => ['D not passed.'],
                ],
            ],
            'attributes, multiple StopOnError rules combined with other top level rules, skipOnError: true' => [
                [],
                [
                    'a' => new Required(),
                    'b' => new StopOnError(
                        [
                            new Required(),
                            new Number(min: 7),
                        ],
                        skipOnError: true,
                    ),
                    'c' => new StopOnError(
                        [
                            new Required(),
                            new Number(min: 42),
                        ],
                        skipOnError: true,
                    ),
                    'd' => new Required(),
                ],
                [
                    'a' => ['A not passed.'],
                    'd' => ['D not passed.'],
                ],
            ],
            'check for missing data set' => [
                ['b' => null],
                [
                    'a' => new StopOnError([
                        new Required(),
                    ]),
                    'b' => new Required(),
                ],
                [
                    'a' => ['A not passed.'],
                    'b' => ['B cannot be blank.'],
                ],
            ],
            'rules normalization, callable' => [
                [],
                new StopOnError([
                    static fn (): Result => (new Result())->addError('Custom error.'),
                ]),
                ['' => ['Custom error.']],
            ],
        ];
    }

    public function testWhen(): void
    {
        $when = static fn (mixed $value): bool => $value !== null;
        $this->testWhenInternal(
            new StopOnError([new Length(min: 10)]),
            new StopOnError([new Length(min: 10)], when: $when),
        );
    }

    protected function getDifferentRuleInHandlerItems(): array
    {
        return [StopOnError::class, StopOnErrorHandler::class];
    }
}
