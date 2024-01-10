<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\Callback;
use Yiisoft\Validator\Rule\Composite;
use Yiisoft\Validator\Rule\CompositeHandler;
use Yiisoft\Validator\Rule\Equal;
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

    public function dataOptions(): array
    {
        return [
            [
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
                                'template' => 'The allowed types are integer, float and string.',
                                'parameters' => [],
                            ],
                            'notNumberMessage' => [
                                'template' => '{Attribute} must be a number.',
                                'parameters' => [],
                            ],
                            'lessThanMinMessage' => [
                                'template' => '{Attribute} must be no less than {min}.',
                                'parameters' => ['min' => null],
                            ],
                            'greaterThanMaxMessage' => [
                                'template' => '{Attribute} must be no greater than {max}.',
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
                                'template' => 'The allowed types are integer, float and string.',
                                'parameters' => [],
                            ],
                            'notNumberMessage' => [
                                'template' => '{Attribute} must be a number.',
                                'parameters' => [],
                            ],
                            'lessThanMinMessage' => [
                                'template' => '{Attribute} must be no less than {min}.',
                                'parameters' => ['min' => null],
                            ],
                            'greaterThanMaxMessage' => [
                                'template' => '{Attribute} must be no greater than {max}.',
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
                                'template' => 'The allowed types are integer, float and string.',
                                'parameters' => [],
                            ],
                            'notNumberMessage' => [
                                'template' => '{Attribute} must be a number.',
                                'parameters' => [],
                            ],
                            'lessThanMinMessage' => [
                                'template' => '{Attribute} must be no less than {min}.',
                                'parameters' => [
                                    'min' => null,
                                ],
                            ],
                            'greaterThanMaxMessage' => [
                                'template' => '{Attribute} must be no greater than {max}.',
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
                    static fn () => (new Result())->addError('Bad value.'),
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
                new class () extends Composite {
                    public function getRules(): iterable
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
                                'template' => '{Attribute} cannot be blank.',
                                'parameters' => [],
                            ],
                            'notPassedMessage' => [
                                'template' => '{Attribute} not passed.',
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

    public function dataValidationPassed(): array
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
                        when: fn () => false,
                    ),
                ],
            ],
            'override constructor' => [
                20,
                [
                    new class () extends Composite {
                        public function __construct()
                        {
                        }
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
            'multiple attributes via subclass' => [
                ['latitude' => -90, 'longitude' => 180],
                [new CoordinatesRuleSet()],
            ],
        ];
    }

    public function dataValidationFailed(): array
    {
        return [
            'callable' => [
                20,
                [
                    new Composite([
                        static fn () => (new Result())->addError('Bad value.'),
                        static fn () => (new Result())->addError('Very bad value.'),
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
                        when: fn () => true,
                    ),
                ],
                ['' => ['Custom error']],
            ],
            'override constructor' => [
                null,
                [
                    new class () extends Composite {
                        public function __construct()
                        {
                            $this->rules = [new Required()];
                        }
                    },
                ],
                ['' => ['Value cannot be blank.']],
            ],
            'multiple attributes' => [
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
        $when = static fn (mixed $value): bool => $value !== null;
        $this->testWhenInternal(new Composite([]), new Composite([], when: $when));
    }

    protected function getDifferentRuleInHandlerItems(): array
    {
        return [Composite::class, CompositeHandler::class];
    }
}
