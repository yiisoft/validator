<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\Callback;
use Yiisoft\Validator\Rule\Composite;
use Yiisoft\Validator\Rule\CompositeHandler;
use Yiisoft\Validator\Rule\Each;
use Yiisoft\Validator\Rule\Equal;
use Yiisoft\Validator\Rule\Nested;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\Tests\Rule\Base\DifferentRuleInHandlerTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\RuleTestCase;
use Yiisoft\Validator\Tests\Rule\Base\RuleWithOptionsTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\RuleWithProvidedRulesTrait;
use Yiisoft\Validator\Tests\Rule\Base\SkipOnErrorTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\WhenTestTrait;
use Yiisoft\Validator\Tests\Support\Rule\CoordinatesRuleSet;
use Yiisoft\Validator\Tests\Support\Rule\RuleWithoutOptions;
use Yiisoft\Validator\Tests\Support\Data\CompositeWithCallbackAttribute;
use Yiisoft\Validator\Tests\Support\Data\PostValidationHookCounter;
use Yiisoft\Validator\ValidationContext;
use Yiisoft\Validator\Validator;

final class CompositeTest extends RuleTestCase
{
    use DifferentRuleInHandlerTestTrait;
    use RuleWithOptionsTestTrait;
    use RuleWithProvidedRulesTrait;
    use SkipOnErrorTestTrait;
    use WhenTestTrait;

    public function testGetName(): void
    {
        $rule = new Composite([]);
        $this->assertSame(Composite::class, $rule->getName());
    }

    public static function dataOptions(): array
    {
        return [
            'basic' => [
                new Composite([
                    new Number(max: 13, pattern: '/1/'),
                    new Number(max: 14, pattern: '/2/'),
                ]),
                [
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                    'rules' => [
                        [
                            Number::class,
                            'min' => null,
                            'max' => 13,
                            'incorrectInputMessage' => [
                                'template' => 'The allowed types for {property} are integer, float and string. '
                                    . '{type} given.',
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
                        [
                            Number::class,
                            'min' => null,
                            'max' => 14,
                            'incorrectInputMessage' => [
                                'template' => 'The allowed types for {property} are integer, float and string. '
                                    . '{type} given.',
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
            'rule without options' => [
                new Composite([
                    new Number(max: 13, pattern: '/1/'),
                    new RuleWithoutOptions(),
                ]),
                [
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                    'rules' => [
                        [
                            Number::class,
                            'min' => null,
                            'max' => 13,
                            'incorrectInputMessage' => [
                                'template' => 'The allowed types for {property} are integer, float and string. '
                                    . '{type} given.',
                                'parameters' => [],
                            ],
                            'notNumberMessage' => [
                                'template' => '{Property} must be a number.',
                                'parameters' => [],
                            ],
                            'lessThanMinMessage' => [
                                'template' => '{Property} must be no less than {min}.',
                                'parameters' => [
                                    'min' => null,
                                ],
                            ],
                            'greaterThanMaxMessage' => [
                                'template' => '{Property} must be no greater than {max}.',
                                'parameters' => [
                                    'max' => 13,
                                ],
                            ],
                            'skipOnEmpty' => false,
                            'skipOnError' => false,
                            'pattern' => '/1/',
                        ],
                        [
                            RuleWithoutOptions::class,
                        ],
                    ],
                ],
            ],
            'callable' => [
                new Composite([
                    static fn() => (new Result())->addError('Bad value.'),
                ]),
                [
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                    'rules' => [
                        [
                            Callback::class,
                            'method' => null,
                            'skipOnEmpty' => false,
                            'skipOnError' => false,
                        ],
                    ],
                ],
            ],
            'inheritance' => [
                new class extends Composite {
                    public function getRules(): array
                    {
                        return [
                            new Required(),
                        ];
                    }

                    public function getOptions(): array
                    {
                        return [
                            'specific-key' => 42,
                            'rules' => $this->dumpRulesAsArray(),
                        ];
                    }
                },
                [
                    'specific-key' => 42,
                    'rules' => [
                        [
                            Required::class,
                            'message' => [
                                'template' => '{Property} cannot be blank.',
                                'parameters' => [],
                            ],
                            'notPassedMessage' => [
                                'template' => '{Property} not passed.',
                                'parameters' => [],
                            ],
                            'skipOnError' => false,
                        ],
                    ],
                ],
            ],
        ];
    }

    public function testGetOptionsWithNotRule(): void
    {
        $this->testGetOptionsWithNotRuleInternal(Composite::class);
    }

    public static function dataValidationPassed(): array
    {
        return [
            [
                20,
                [
                    new Composite(
                        [
                            new Number(max: 13),
                            new Number(max: 14),
                        ],
                        when: fn() => false,
                    ),
                ],
            ],
            'override constructor' => [
                20,
                [
                    new class extends Composite {
                        public function __construct() {}
                    },
                ],
            ],
            [
                null,
                [
                    new Composite(
                        [
                            new Number(max: 13),
                            new Number(max: 14),
                        ],
                        skipOnEmpty: true,
                    ),
                ],
            ],
            'multiple properties via subclass' => [
                ['latitude' => -90, 'longitude' => 180],
                [new CoordinatesRuleSet()],
            ],
        ];
    }

    public static function dataValidationFailed(): array
    {
        return [
            'callable' => [
                20,
                [
                    new Composite([
                        static fn() => (new Result())->addError('Bad value.'),
                        static fn() => (new Result())->addError('Very bad value.'),
                    ]),
                ],
                [
                    '' => [
                        'Bad value.',
                        'Very bad value.',
                    ],
                ],
            ],
            'when true' => [
                20,
                [
                    new Composite(
                        [new Number(max: 13), new Number(min: 21)],
                        when: fn() => true,
                    ),
                ],
                [
                    '' => [
                        'Value must be no greater than 13.',
                        'Value must be no less than 21.',
                    ],
                ],
            ],
            'skip on error with previous error' => [
                20,
                [
                    new Equal(19),
                    new Composite(
                        [new Number(max: 13)],
                        skipOnError: true,
                    ),
                ],
                [
                    '' => ['Value must be equal to "19".'],
                ],
            ],
            'skip on error without previous error' => [
                20,
                [
                    new Composite(
                        [new Number(max: 13)],
                        skipOnError: true,
                    ),
                ],
                [
                    '' => ['Value must be no greater than 13.'],
                ],
            ],
            'custom error' => [
                20,
                [
                    new Composite(
                        [new Number(max: 13, greaterThanMaxMessage: 'Custom error')],
                        when: fn() => true,
                    ),
                ],
                ['' => ['Custom error']],
            ],
            'override rules' => [
                null,
                [
                    new class extends Composite {
                        public function getRules(): array
                        {
                            return [new Required()];
                        }
                    },
                ],
                ['' => ['Value cannot be blank.']],
            ],
            'multiple properties' => [
                ['latitude' => -91, 'longitude' => 181],
                [new CoordinatesRuleSet()],
                [
                    'latitude' => ['Latitude must be no less than -90.'],
                    'longitude' => ['Longitude must be no greater than 180.'],
                ],
            ],
        ];
    }

    public function testSkipOnError(): void
    {
        $this->testSkipOnErrorInternal(new Composite([]), new Composite([], skipOnError: true));
    }

    public function testWhen(): void
    {
        $when = static fn(mixed $value): bool => $value !== null;
        $this->testWhenInternal(new Composite([]), new Composite([], when: $when));
    }

    public function testDataSetAndPropertyInInnerRules(): void
    {
        $data = ['a' => 'x', 'b' => 1];
        $innerData = null;
        $innerProperty = null;

        (new Validator())->validate($data, [
            'a' => new Composite([
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
            $createRules(static fn(Callback $callback): Composite => new Composite([$callback])),
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
            ['o' => new Composite([new Each([new Number(max: 10)])])],
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
            [new Composite([new Callback(static fn(): Result => new Result())])],
        );

        $this->assertSame(1, $data->hookCallsCount);
    }

    public function testPostValidationHookOfPropertyValueInInnerRules(): void
    {
        $value = new PostValidationHookCounter();

        (new Validator())->validate(
            ['o' => $value],
            ['o' => new Composite([new Callback(static fn(): Result => new Result())])],
        );

        $this->assertSame(0, $value->hookCallsCount);
    }

    public function testWithCallbackAttribute(): void
    {
        $result = (new Validator())->validate(new CompositeWithCallbackAttribute());

        $this->assertSame(
            [
                '' => ['Invalid A.'],
                'b' => ['Invalid B.'],
            ],
            $result->getErrorMessagesIndexedByProperty(),
        );
    }

    protected function getDifferentRuleInHandlerItems(): array
    {
        return [Composite::class, CompositeHandler::class];
    }
}
