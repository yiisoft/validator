<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests;

use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\Error;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\Each;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\RuleInterface;
use Yiisoft\Validator\RuleSet;
use Yiisoft\Validator\Tests\Stub\CustomUrlRule;
use Yiisoft\Validator\ValidationContext;
use function get_class;

class RuleSetTest extends TestCase
{
    public function testMethodSyntax(): void
    {
        $ruleSet = new RuleSet();
        $ruleSet->add(new Required());
        $ruleSet->add(new Number(max: 10));

        $result = $ruleSet->validate(42);
        $this->assertCount(1, $result->getErrors());
    }

    public function testArraySyntax(): void
    {
        $ruleSet = new RuleSet([new Required(), new Number(max: 10)]);
        $result = $ruleSet->validate(42);

        $this->assertCount(1, $result->getErrors());
    }

    public function testCallback(): void
    {
        $ruleSet = new RuleSet(
            [
                static function ($value): Result {
                    $result = new Result();
                    if ($value !== 42) {
                        $result->addError('Value should be 42!');
                    }

                    return $result;
                },
            ]
        );

        $result = $ruleSet->validate(41);
        $this->assertCount(1, $result->getErrors());
    }

    public function testWhenValidate(): void
    {
        $ruleSet = new RuleSet([
            new Number(min: 10),
            new Number(when: fn () => false, min: 10),
            new Number(min: 10),
        ]);
        $result = $ruleSet->validate(1);

        $this->assertCount(2, $result->getErrors());
    }

    public function testSkipOnError(): void
    {
        $ruleSet = new RuleSet([
            new Number(min: 10),
            new Number(min: 10, skipOnError: true),
            new Number(min: 10),
        ]);
        $result = $ruleSet->validate(1);

        $this->assertCount(2, $result->getErrors());
    }

    public function asArrayDataProvider(): array
    {
        $rule = new class () implements RuleInterface {
            public function validate($value, ValidationContext $context = null): Result
            {
            }
        };

        $ruleSet = new RuleSet();
        $ruleSet->add(new Required());
        $ruleSet->add(new Number(max: 10));
        $ruleSet->add($rule);

        return [
            [
                $ruleSet,
                [
                    [
                        'required',
                        'message' => 'Value cannot be blank.',
                        'skipOnEmpty' => false,
                        'skipOnError' => false,
                    ],
                    [
                        'number',
                        'asInteger' => false,
                        'min' => null,
                        'max' => 10,
                        'notANumberMessage' => 'Value must be a number.',
                        'tooSmallMessage' => 'Value must be no less than .',
                        'tooBigMessage' => 'Value must be no greater than 10.',
                        'skipOnEmpty' => false,
                        'skipOnError' => false,
                        'integerPattern' => '/^\s*[+-]?\d+\s*$/',
                        'numberPattern' => '/^\s*[-+]?\d*\.?\d+([eE][-+]?\d+)?\s*$/'
                    ],
                    [
                        get_class($rule),
                    ],
                ],
            ],
            [
                new RuleSet([new Number(min: 10), new Number(min: 10), new Number(asInteger: true, min: 10)]),
                [
                    [
                        'number',
                        'asInteger' => false,
                        'min' => 10,
                        'max' => null,
                        'notANumberMessage' => 'Value must be a number.',
                        'tooSmallMessage' => 'Value must be no less than 10.',
                        'tooBigMessage' => 'Value must be no greater than .',
                        'skipOnEmpty' => false,
                        'skipOnError' => false,
                        'integerPattern' => '/^\s*[+-]?\d+\s*$/',
                        'numberPattern' => '/^\s*[-+]?\d*\.?\d+([eE][-+]?\d+)?\s*$/'
                    ],
                    [
                        'number',
                        'asInteger' => false,
                        'min' => 10,
                        'max' => null,
                        'notANumberMessage' => 'Value must be a number.',
                        'tooSmallMessage' => 'Value must be no less than 10.',
                        'tooBigMessage' => 'Value must be no greater than .',
                        'skipOnEmpty' => false,
                        'skipOnError' => false,
                        'integerPattern' => '/^\s*[+-]?\d+\s*$/',
                        'numberPattern' => '/^\s*[-+]?\d*\.?\d+([eE][-+]?\d+)?\s*$/'
                    ],
                    [
                        'number',
                        'asInteger' => true,
                        'min' => 10,
                        'max' => null,
                        'notANumberMessage' => 'Value must be an integer.',
                        'tooSmallMessage' => 'Value must be no less than 10.',
                        'tooBigMessage' => 'Value must be no greater than .',
                        'skipOnEmpty' => false,
                        'skipOnError' => false,
                        'integerPattern' => '/^\s*[+-]?\d+\s*$/',
                        'numberPattern' => '/^\s*[-+]?\d*\.?\d+([eE][-+]?\d+)?\s*$/'
                    ],
                ],
            ],
            [
                new RuleSet([
                    new Each([new Number(max: 13), new Number(max: 14)]),
                    new Number(min: 10),
                ]),
                [
                    [
                        'each',
                        [
                            'number',
                            'asInteger' => false,
                            'min' => null,
                            'max' => 13,
                            'notANumberMessage' => 'Value must be a number.',
                            'tooSmallMessage' => 'Value must be no less than .',
                            'tooBigMessage' => 'Value must be no greater than 13.',
                            'skipOnEmpty' => false,
                            'skipOnError' => false,
                            'integerPattern' => '/^\s*[+-]?\d+\s*$/',
                            'numberPattern' => '/^\s*[-+]?\d*\.?\d+([eE][-+]?\d+)?\s*$/'
                        ],
                        [
                            'number',
                            'asInteger' => false,
                            'min' => null,
                            'max' => 14,
                            'notANumberMessage' => 'Value must be a number.',
                            'tooSmallMessage' => 'Value must be no less than .',
                            'tooBigMessage' => 'Value must be no greater than 14.',
                            'skipOnEmpty' => false,
                            'skipOnError' => false,
                            'integerPattern' => '/^\s*[+-]?\d+\s*$/',
                            'numberPattern' => '/^\s*[-+]?\d*\.?\d+([eE][-+]?\d+)?\s*$/'
                        ],
                    ],
                    [
                        'number',
                        'asInteger' => false,
                        'min' => 10,
                        'max' => null,
                        'notANumberMessage' => 'Value must be a number.',
                        'tooSmallMessage' => 'Value must be no less than 10.',
                        'tooBigMessage' => 'Value must be no greater than .',
                        'skipOnEmpty' => false,
                        'skipOnError' => false,
                        'integerPattern' => '/^\s*[+-]?\d+\s*$/',
                        'numberPattern' => '/^\s*[-+]?\d*\.?\d+([eE][-+]?\d+)?\s*$/'
                    ],
                ],
            ],
        ];
    }

    /**
     * @dataProvider asArrayDataProvider
     */
    public function testAsArray(RuleSet $ruleSet, array $expectedArray): void
    {
        $this->assertEquals($expectedArray, $ruleSet->asArray());
    }

    public function testAsArrayWithGroupRule(): void
    {
        $ruleSet = new RuleSet();
        $ruleSet->add(new Required());
        $ruleSet->add(new CustomUrlRule());

        $this->assertEquals([
            [
                'required',
                'message' => 'Value cannot be blank.',
                'skipOnEmpty' => false,
                'skipOnError' => false,
            ],
            [
                'customUrlRule',
                [
                    'required',
                    'message' => 'Value cannot be blank.',
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
                [
                    'url',
                    'message' => 'This value is not a valid URL.',
                    'enableIDN' => true,
                    'validSchemes' => ['http', 'https',],
                    'pattern' => '/^{schemes}:\/\/(([a-zA-Z0-9][a-zA-Z0-9_-]*)(\.[a-zA-Z0-9][a-zA-Z0-9_-]*)+)(?::\d{1,5})?([?\/#].*$|$)/',
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
                [
                    'hasLength',
                    'message' => 'This value must be a string.',
                    'min' => null,
                    'tooShortMessage' => 'This value should contain at least {min, number} {min, plural, one{character} other{characters}}.',
                    'max' => 20,
                    'tooLongMessage' => 'This value should contain at most {max, number} {max, plural, one{character} other{characters}}.',
                    'encoding' => 'UTF-8',
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
        ], $ruleSet->asArray());
    }

    public function testPersistentError(): void
    {
        $ruleSet = new RuleSet([
            static function ($value): Result {
                $result = new Result();
                $result->addError('e1')
                    ->addError('e2')
                    ->addError('e3');

                return $result;
            },
            static function ($value): Result {
                $result = new Result();
                $result->addError('e4')
                    ->addError('e5')
                    ->addError('e6');

                return $result;
            },
        ]);
        $result = $ruleSet->validate('hi');

        $this->assertFalse($result->isValid());
        $this->assertEquals([
            new Error('e1'),
            new Error('e2'),
            new Error('e3'),
            new Error('e4'),
            new Error('e5'),
            new Error('e6'),
        ], $result->getErrors());
    }

    public function testAddErrorWithValuePath(): void
    {
        $ruleSet = new RuleSet([
            static function ($value): Result {
                $result = new Result();
                $result->addError('e1', ['key1']);

                return $result;
            },
        ]);
        $result = $ruleSet->validate('hi');
        $result->addError('e2', ['key2']);

        $this->assertEquals([new Error('e1', ['key1']), new Error('e2', ['key2'])], $result->getErrors());
    }
}
