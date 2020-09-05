<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests;

use Yiisoft\Validator\Rule\Each;
use Yiisoft\Validator\Rules;
use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Rule\Required;

class RulesTest extends TestCase
{
    public function testMethodSyntax(): void
    {
        $rules = new Rules();
        $rules->add(new Required());
        $rules->add((new Number())->max(10));

        $result = $rules->validate(42);
        $this->assertFalse($result->isValid());
        $this->assertCount(1, $result->getErrors());
    }

    public function testArraySyntax(): void
    {
        $rules = new Rules(
            [
                new Required(),
                (new Number())->max(10)
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
                }
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
                (new Number())->min(10),
                (new Number())->min(10)->when(fn () => false)->skipOnError(false),
                (new Number())->min(10)->skipOnError(false)
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
                (new Number())->min(10),
                (new Number())->min(10)->skipOnError(false),
                (new Number())->min(10)
            ]
        );

        $result = $rules->validate(1);

        $this->assertFalse($result->isValid());
        $this->assertCount(2, $result->getErrors());
    }

    public function testAsArray(): void
    {
        $rules = new Rules();
        $rules->add(new Required());
        $rules->add((new Number())->max(10));

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
            ]
        ], $rules->asArray());

        $rules = new Rules(
            [
                (new Number())->min(10),
                (new Number())->min(10)->skipOnError(false),
                (new Number())->min(10)->integer()
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
            (new Each(new Rules([
                (new Number())->max(13),
                (new Number())->max(14)
            ]))),
            (new Number())->min(10),
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
                ]
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
}
