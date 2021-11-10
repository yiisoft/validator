<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests;

use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\Each;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\Rules;
use Yiisoft\Validator\Tests\Stub\CustomUrlRule;

class RulesTest extends TestCase
{
    public function testMethodSyntax(): void
    {
        $rules = new Rules();
        $rules->add(Required::rule());
        $rules->add(Number::rule()->max(10));

        $result = $rules->validate(42);
        $this->assertFalse($result->isValid());
        $this->assertCount(1, $result->getErrors());
    }

    public function testArraySyntax(): void
    {
        $rules = new Rules(
            [
                Required::rule(),
                Number::rule()->max(10),
            ]
        );

        $result = $rules->validate(42);
        $this->assertFalse($result->isValid());
        $this->assertCount(1, $result->getErrors());
    }

    public function testCallback(): void
    {
        $rules = new Rules(
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

        $result = $rules->validate(41);
        $this->assertFalse($result->isValid());
        $this->assertCount(1, $result->getErrors());
    }

    public function testWhenValidate(): void
    {
        $rules = new Rules(
            [
                Number::rule()->min(10),
                Number::rule()->min(10)->when(fn () => false)->skipOnError(false),
                Number::rule()->min(10)->skipOnError(false),
            ]
        );

        $result = $rules->validate(1);

        $this->assertFalse($result->isValid());
        $this->assertCount(2, $result->getErrors());
    }

    public function testSkipOnError(): void
    {
        $rules = new Rules(
            [
                Number::rule()->min(10),
                Number::rule()->min(10)->skipOnError(false),
                Number::rule()->min(10),
            ]
        );

        $result = $rules->validate(1);

        $this->assertFalse($result->isValid());
        $this->assertCount(2, $result->getErrors());
    }

    public function testAsArray(): void
    {
        $rules = new Rules();
        $rules->add(Required::rule());
        $rules->add(Number::rule()->max(10));

        $this->assertEquals([
            [
                'required',
                'message' => 'Value cannot be blank.',
                'skipOnEmpty' => false,
                'skipOnError' => true,
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
                'skipOnError' => true,
            ],
        ], $rules->asArray());

        $rules = new Rules(
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
                'skipOnError' => true,
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
                'skipOnError' => true,
            ],
        ], $rules->asArray());

        $rules = new Rules([
            Each::rule(new Rules([
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
                    'skipOnError' => true,
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
                    'skipOnError' => true,
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
                'skipOnError' => true,
            ],
        ], $rules->asArray());
    }

    public function testAsArrayWithGroupRule(): void
    {
        $rules = new Rules();
        $rules->add(Required::rule());
        $rules->add(CustomUrlRule::rule());

        $this->assertEquals([
            [
                'required',
                'message' => 'Value cannot be blank.',
                'skipOnEmpty' => false,
                'skipOnError' => true,
            ],
            [
                'customUrlRule',
                [
                    'required',
                    'message' => 'Value cannot be blank.',
                    'skipOnEmpty' => false,
                    'skipOnError' => true,
                ],
                [
                    'url',
                    'message' => 'This value is not a valid URL.',
                    'enableIDN' => true,
                    'validSchemes' => ['http', 'https',],
                    'pattern' => '/^{schemes}:\/\/(([a-zA-Z0-9][a-zA-Z0-9_-]*)(\.[a-zA-Z0-9][a-zA-Z0-9_-]*)+)(?::\d{1,5})?([?\/#].*$|$)/',
                    'skipOnEmpty' => false,
                    'skipOnError' => true,
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
                    'skipOnError' => true,
                ],
            ],
        ], $rules->asArray());
    }
}
