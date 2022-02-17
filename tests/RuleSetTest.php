<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests;

use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\Error;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\Callback;
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
        $ruleSet->add(Required::rule());
        $ruleSet->add(Number::rule()->max(10));

        $result = $ruleSet->validate(42);
        $this->assertFalse($result->isValid());
        $this->assertCount(1, $result->getErrors());
    }

    public function testArraySyntax(): void
    {
        $ruleSet = new RuleSet([
            Required::rule(),
            Number::rule()->max(10),
        ]);
        $result = $ruleSet->validate(42);

        $this->assertFalse($result->isValid());
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
        $this->assertFalse($result->isValid());
        $this->assertCount(1, $result->getErrors());
    }

    public function testWhenValidate(): void
    {
        $ruleSet = new RuleSet([
            Number::rule()->min(10),
            Number::rule()->min(10)->when(fn () => false)->skipOnError(false),
            Number::rule()->min(10)->skipOnError(false),
        ]);
        $result = $ruleSet->validate(1);

        $this->assertFalse($result->isValid());
        $this->assertCount(2, $result->getErrors());
    }

    public function testSkipOnError(): void
    {
        $ruleSet = new RuleSet([
            Number::rule()->min(10),
            Number::rule()->min(10)->skipOnError(true),
            Number::rule()->min(10),
        ]);
        $result = $ruleSet->validate(1);

        $this->assertFalse($result->isValid());
        $this->assertCount(2, $result->getErrors());
    }

    public function testAsArray(): void
    {
        $rule = new class () implements RuleInterface {
            public function validate($value, ValidationContext $context = null): Result
            {
            }
        };

        $ruleSet = new RuleSet();
        $ruleSet->add(Required::rule());
        $ruleSet->add(Number::rule()->max(10));
        $ruleSet->add($rule);

        $this->assertEquals([
            [
                'required',
                'message' => 'Value cannot be blank.',
                'skipOnEmpty' => false,
                'skipOnError' => false,
            ],
            [
                'number',
                'notANumberMessage' => 'Value must be a number.',
                'asInteger' => false,
                'min' => null,
                'tooSmallMessage' => 'Value must be no less than .',
                'max' => 10,
                'tooBigMessage' => 'Value must be no greater than 10.',
                'skipOnEmpty' => false,
                'skipOnError' => false,
            ],
            [
                get_class($rule),
            ],
        ], $ruleSet->asArray());

        $ruleSet = new RuleSet(
            [
                Number::rule()->min(10),
                Number::rule()->min(10)->skipOnError(false),
                Number::rule()->min(10)->integer(),
            ]
        );
        $this->assertEquals([
            [
                'number',
                'notANumberMessage' => 'Value must be a number.',
                'asInteger' => false,
                'min' => 10,
                'tooSmallMessage' => 'Value must be no less than 10.',
                'max' => null,
                'tooBigMessage' => 'Value must be no greater than .',
                'skipOnEmpty' => false,
                'skipOnError' => false,
            ],
            [
                'number',
                'notANumberMessage' => 'Value must be a number.',
                'asInteger' => false,
                'min' => 10,
                'tooSmallMessage' => 'Value must be no less than 10.',
                'max' => null,
                'tooBigMessage' => 'Value must be no greater than .',
                'skipOnEmpty' => false,
                'skipOnError' => false,
            ],
            [
                'number',
                'notANumberMessage' => 'Value must be an integer.',
                'asInteger' => true,
                'min' => 10,
                'tooSmallMessage' => 'Value must be no less than 10.',
                'max' => null,
                'tooBigMessage' => 'Value must be no greater than .',
                'skipOnEmpty' => false,
                'skipOnError' => false,
            ],
        ], $ruleSet->asArray());

        $ruleSet = new RuleSet([
            Each::rule(new RuleSet([
                Number::rule()->max(13),
                Number::rule()->max(14),
            ])),
            Number::rule()->min(10),
        ]);

        $this->assertEquals([
            ['each',
                [
                    'number',
                    'notANumberMessage' => 'Value must be a number.',
                    'asInteger' => false,
                    'min' => null,
                    'tooSmallMessage' => 'Value must be no less than .',
                    'max' => 13,
                    'tooBigMessage' => 'Value must be no greater than 13.',
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
                [
                    'number',
                    'notANumberMessage' => 'Value must be a number.',
                    'asInteger' => false,
                    'min' => null,
                    'tooSmallMessage' => 'Value must be no less than .',
                    'max' => 14,
                    'tooBigMessage' => 'Value must be no greater than 14.',
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                'number',
                'notANumberMessage' => 'Value must be a number.',
                'asInteger' => false,
                'min' => 10,
                'tooSmallMessage' => 'Value must be no less than 10.',
                'max' => null,
                'tooBigMessage' => 'Value must be no greater than .',
                'skipOnEmpty' => false,
                'skipOnError' => false,
            ],
        ], $ruleSet->asArray());
    }

    public function testAsArrayWithGroupRule(): void
    {
        $ruleSet = new RuleSet();
        $ruleSet->add(Required::rule());
        $ruleSet->add(CustomUrlRule::rule());

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
            Callback::rule(static function ($value): Result {
                $result = new Result();
                $result->addError('e1');
                $result->addError('e2');
                $result->addError('e3');

                return $result;
            }),
            Callback::rule(static function ($value): Result {
                $result = new Result();
                $result->addError('e4');
                $result->addError('e5');
                $result->addError('e6');

                return $result;
            })->skipOnError(false),
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
            Callback::rule(static function ($value): Result {
                $result = new Result();
                $result->addError('e1', ['key1']);

                return $result;
            }),
        ]);
        $result = $ruleSet->validate('hi');
        $result->addError('e2', ['key2']);

        $this->assertEquals([new Error('e1', ['key1']), new Error('e2', ['key2'])], $result->getErrors());
    }
}
