<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\Callback;
use Yiisoft\Validator\Rule\Each;
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\Rule\Nested;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\Rule\StopOnError;
use Yiisoft\Validator\Rule\StopOnErrorHandler;
use Yiisoft\Validator\Tests\Rule\Base\DifferentRuleInHandlerTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\RuleTestCase;
use Yiisoft\Validator\Tests\Rule\Base\RuleWithOptionsTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\RuleWithProvidedRulesTrait;
use Yiisoft\Validator\Tests\Rule\Base\WhenTestTrait;
use Yiisoft\Validator\Tests\Support\Data\PostValidationHookCounter;
use Yiisoft\Validator\Tests\Support\Data\StopOnErrorDto;
use Yiisoft\Validator\ValidationContext;
use Yiisoft\Validator\Validator;

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

    public static function dataOptions(): array
    {
        return [
            'basic' => [
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
                                'template' => '{Property} must contain at least {min, number} {min, plural, '
                                    . 'one{character} other{characters}}.',
                                'parameters' => [
                                    'min' => 10,
                                ],
                            ],
                            'greaterThanMaxMessage' => [
                                'template' => '{Property} must contain at most {max, number} {max, plural, '
                                    . 'one{character} other{characters}}.',
                                'parameters' => [
                                    'max' => null,
                                ],
                            ],
                            'notExactlyMessage' => [
                                'template' => '{Property} must contain exactly {exactly, number} {exactly, plural, '
                                    . 'one{character} other{characters}}.',
                                'parameters' => [
                                    'exactly' => null,
                                ],
                            ],
                            'incorrectInputMessage' => [
                                'template' => '{Property} must be a string. {type} given.',
                                'parameters' => [],
                            ],
                            'encoding' => 'UTF-8',
                            'skipOnEmpty' => false,
                            'skipOnError' => false,
                        ],
                    ],
                ],
            ],
            'custom' => [
                new StopOnError(
                    [new Length(min: 10)],
                    skipOnEmpty: true,
                    skipOnError: true,
                ),
                [
                    'skipOnEmpty' => true,
                    'skipOnError' => true,
                    'rules' => [
                        [
                            Length::class,
                            'min' => 10,
                            'max' => null,
                            'exactly' => null,
                            'lessThanMinMessage' => [
                                'template' => '{Property} must contain at least {min, number} {min, plural, '
                                    . 'one{character} other{characters}}.',
                                'parameters' => [
                                    'min' => 10,
                                ],
                            ],
                            'greaterThanMaxMessage' => [
                                'template' => '{Property} must contain at most {max, number} {max, plural, '
                                    . 'one{character} other{characters}}.',
                                'parameters' => [
                                    'max' => null,
                                ],
                            ],
                            'notExactlyMessage' => [
                                'template' => '{Property} must contain exactly {exactly, number} {exactly, plural, '
                                    . 'one{character} other{characters}}.',
                                'parameters' => [
                                    'exactly' => null,
                                ],
                            ],
                            'incorrectInputMessage' => [
                                'template' => '{Property} must be a string. {type} given.',
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

    public static function dataValidationPassed(): array
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

    public static function dataValidationFailed(): array
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
            'properties, multiple StopOnError rules combined with other top level rules' => [
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
            'properties, multiple StopOnError rules combined with other top level rules, skipOnError: true' => [
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
                    static fn(): Result => (new Result())->addError('Custom error.'),
                ]),
                ['' => ['Custom error.']],
            ],
        ];
    }

    public function testWhen(): void
    {
        $when = static fn(mixed $value): bool => $value !== null;
        $this->testWhenInternal(
            new StopOnError([new Length(min: 10)]),
            new StopOnError([new Length(min: 10)], when: $when),
        );
    }

    public function testDataSetAndPropertyInInnerRules(): void
    {
        $data = ['a' => 'x', 'b' => 1];
        $innerData = null;
        $innerProperty = null;

        (new Validator())->validate($data, [
            'a' => new StopOnError([
                new Callback(
                    static function (mixed $value, Callback $rule, ValidationContext $context) use (
                        &$innerData,
                        &$innerProperty,
                    ): Result {
                        $innerData = $context->getDataSet()->getData();
                        $innerProperty = $context->getProperty();
                        return new Result();
                    },
                ),
            ]),
        ]);

        $this->assertSame($data, $innerData);
        $this->assertSame('a', $innerProperty);
    }

    public function testDataSetInInnerRulesWithNestedEach(): void
    {
        $data = [
            'groups' => [
                ['items' => [['a' => 1], ['a' => 2]]],
                ['items' => [['a' => 3]]],
            ],
        ];

        $createRules = static function (callable $wrap) use (&$innerData): array {
            $callback = new Callback(
                static function (mixed $value, Callback $rule, ValidationContext $context) use (&$innerData): Result {
                    $innerData[] = [$context->getDataSet()->getData(), $context->getProperty()];
                    return new Result();
                },
            );
            return [
                'groups' => new Each(
                    new Nested([
                        'items' => new Each(
                            new Nested(['a' => $wrap($callback)]),
                        ),
                    ]),
                ),
            ];
        };

        $innerData = [];
        (new Validator())->validate(
            $data,
            $createRules(static fn(Callback $callback): Callback => $callback),
        );
        $expectedInnerData = $innerData;

        $innerData = [];
        (new Validator())->validate(
            $data,
            $createRules(static fn(Callback $callback): StopOnError => new StopOnError([$callback])),
        );

        $this->assertSame(
            [
                [['a' => 1], 'a'],
                [['a' => 2], 'a'],
                [['a' => 3], 'a'],
            ],
            $expectedInnerData,
        );
        $this->assertSame($expectedInnerData, $innerData);
    }

    public function testObjectValueInInnerRules(): void
    {
        $result = (new Validator())->validate(
            ['o' => (object) ['x' => 7]],
            ['o' => new StopOnError([new Each([new Number(max: 10)])])],
        );

        $this->assertSame(
            ['o' => ['O must be array or iterable. stdClass given.']],
            $result->getErrorMessagesIndexedByPath(),
        );
    }

    public function testPostValidationHookOfDataInInnerRules(): void
    {
        $data = new PostValidationHookCounter();

        (new Validator())->validate(
            $data,
            [new StopOnError([new Callback(static fn(): Result => new Result())])],
        );

        $this->assertSame(1, $data->hookCallsCount);
    }

    public function testPostValidationHookOfPropertyValueInInnerRules(): void
    {
        $value = new PostValidationHookCounter();

        (new Validator())->validate(
            ['o' => $value],
            ['o' => new StopOnError([new Callback(static fn(): Result => new Result())])],
        );

        $this->assertSame(0, $value->hookCallsCount);
    }

    public function testClassAttribute(): void
    {
        $result = (new Validator())->validate(new StopOnErrorDto());

        $this->assertSame(
            [
                '' => ['error A'],
            ],
            $result->getErrorMessagesIndexedByProperty(),
        );
    }

    protected function getDifferentRuleInHandlerItems(): array
    {
        return [StopOnError::class, StopOnErrorHandler::class];
    }
}
