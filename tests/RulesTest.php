<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests;

use Yiisoft\Validator\Rule\Each;
use Yiisoft\Validator\Rule\InRange;
use Yiisoft\Validator\Rule\When;
use Yiisoft\Validator\Rules;
use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\Error;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\Tests\Stub\CustomUrlRule;

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
                static function ($value): Error {
                    $result = new Error();
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
                (new When(
                    fn() => false,
                    (new Number())->min(10)
                ))->skipOnError(false),
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

    public function testSkipOnErrorGroup(): void
    {
        $rules = new Rules(
            [
                (new Number())->min(10),
                (new Number())->min(10),
                (new Number())->min(10)
            ]
        );

        $result = $rules->skipOnError(false)->validate(1);

        $this->assertFalse($result->isValid());
        $this->assertCount(3, $result->getErrors());
    }

    public function testSkipOnEmptyGroup(): void
    {
        $rules = new Rules(
            [
                (new Number())->min(10),
                (new Number())->max(100)
            ]
        );

        $result = $rules->skipOnEmpty(true)->validate('');

        $this->assertTrue($result->isValid());
        $this->assertCount(0, $result->getErrors());
    }

    public function testAsArray(): void
    {
        $rules = new Rules();
        $rules->add(new Required());
        $rules->add((new Number())->max(10));

        $this->assertEquals([
            0 =>
                [
                    0 => 'required',
                    1 =>
                        [
                            'skipOnEmpty' => false,
                            'skipOnError' => true,
                            'message' => 'Value cannot be blank.',
                        ],
                ],
            1 =>
                [
                    0 => 'number',
                    1 =>
                        [
                            'skipOnEmpty' => false,
                            'skipOnError' => true,
                            'notANumberMessage' => 'Value must be a number.',
                            'asInteger' => false,
                            'min' => NULL,
                            'tooSmallMessage' => 'Value must be no less than {min}.',
                            'max' => 10,
                            'tooBigMessage' => 'Value must be no greater than {max}.',
                        ],
                ],
        ], $rules->getOptions());

        $rules = new Rules(
            [
                (new Number())->min(10),
                (new Number())->min(10)->skipOnError(false),
                (new Number())->min(10)->integer()
            ]
        );
        $this->assertEquals([
            0 =>
                [
                    0 => 'number',
                    1 =>
                        [
                            'skipOnEmpty' => false,
                            'skipOnError' => true,
                            'notANumberMessage' => 'Value must be a number.',
                            'asInteger' => false,
                            'min' => 10,
                            'tooSmallMessage' => 'Value must be no less than {min}.',
                            'max' => NULL,
                            'tooBigMessage' => 'Value must be no greater than {max}.',
                        ],
                ],
            1 =>
                [
                    0 => 'number',
                    1 =>
                        [
                            'skipOnEmpty' => false,
                            'skipOnError' => false,
                            'notANumberMessage' => 'Value must be a number.',
                            'asInteger' => false,
                            'min' => 10,
                            'tooSmallMessage' => 'Value must be no less than {min}.',
                            'max' => NULL,
                            'tooBigMessage' => 'Value must be no greater than {max}.',
                        ],
                ],
            2 =>
                [
                    0 => 'number',
                    1 =>
                        [
                            'skipOnEmpty' => false,
                            'skipOnError' => true,
                            'notANumberMessage' => 'Value must be an integer.',
                            'asInteger' => true,
                            'min' => 10,
                            'tooSmallMessage' => 'Value must be no less than {min}.',
                            'max' => NULL,
                            'tooBigMessage' => 'Value must be no greater than {max}.',
                        ],
                ],
        ], $rules->getOptions());

        $rules = new Rules([
            (new Each(new Rules([
                (new Number())->max(13),
                (new Number())->max(14)
            ]))),
            (new Number())->min(10),
        ]);

        $this->assertEquals([
            0 =>
                [
                    0 => 'each',
                    1 =>
                        [
                            0 =>
                                [
                                    0 => 'number',
                                    1 =>
                                        [
                                            'skipOnEmpty' => false,
                                            'skipOnError' => true,
                                            'notANumberMessage' => 'Value must be a number.',
                                            'asInteger' => false,
                                            'min' => NULL,
                                            'tooSmallMessage' => 'Value must be no less than {min}.',
                                            'max' => 13,
                                            'tooBigMessage' => 'Value must be no greater than {max}.',
                                        ],
                                ],
                            1 =>
                                [
                                    0 => 'number',
                                    1 =>
                                        [
                                            'skipOnEmpty' => false,
                                            'skipOnError' => true,
                                            'notANumberMessage' => 'Value must be a number.',
                                            'asInteger' => false,
                                            'min' => NULL,
                                            'tooSmallMessage' => 'Value must be no less than {min}.',
                                            'max' => 14,
                                            'tooBigMessage' => 'Value must be no greater than {max}.',
                                        ],
                                ],
                        ],
                ],
            1 =>
                [
                    0 => 'number',
                    1 =>
                        [
                            'skipOnEmpty' => false,
                            'skipOnError' => true,
                            'notANumberMessage' => 'Value must be a number.',
                            'asInteger' => false,
                            'min' => 10,
                            'tooSmallMessage' => 'Value must be no less than {min}.',
                            'max' => NULL,
                            'tooBigMessage' => 'Value must be no greater than {max}.',
                        ],
                ],
        ], $rules->getOptions());
    }

    public function testAsArrayWithGroupRule(): void
    {
        $rules = new Rules();
        $rules->add(new Required());
        $rules->add(new CustomUrlRule());

        $this->assertEquals(array(
            0 =>
                array(
                    0 => 'required',
                    1 =>
                        array(
                            'skipOnEmpty' => false,
                            'skipOnError' => true,
                            'message' => 'Value cannot be blank.',
                        ),
                ),
            1 =>
                array(
                    0 => 'customUrlRule',
                    1 =>
                        array(
                            0 =>
                                array(
                                    0 => 'required',
                                    1 =>
                                        array(
                                            'skipOnEmpty' => false,
                                            'skipOnError' => true,
                                            'message' => 'Value cannot be blank.',
                                        ),
                                ),
                            1 =>
                                array(
                                    0 => 'url',
                                    1 =>
                                        array(
                                            'skipOnEmpty' => false,
                                            'skipOnError' => true,
                                            'message' => 'This value is not a valid URL.',
                                            'enableIDN' => true,
                                            'validSchemes' =>
                                                array(
                                                    0 => 'http',
                                                    1 => 'https',
                                                ),
                                            'pattern' => '/^{schemes}:\\/\\/(([A-Z0-9][A-Z0-9_-]*)(\\.[A-Z0-9][A-Z0-9_-]*)+)(?::\\d{1,5})?(?:$|[?\\/#])/i',
                                        ),
                                ),
                            2 =>
                                array(
                                    0 => 'hasLength',
                                    1 =>
                                        array(
                                            'skipOnEmpty' => false,
                                            'skipOnError' => true,
                                            'message' => 'This value must be a string.',
                                            'min' => NULL,
                                            'tooShortMessage' => 'This value should contain at least {min, number} {min, plural, one{character} other{characters}}.',
                                            'max' => 20,
                                            'tooLongMessage' => 'This value should contain at most {max, number} {max, plural, one{character} other{characters}}.',
                                            'encoding' => 'UTF-8',
                                        ),
                                ),
                        ),
                ),
        ), $rules->getOptions());
    }

    public function testGroupNames(): void
    {
        $rules = new Rules(
            [
                (new Number())->min(10),
                (new InRange([10, 20]))->skipOnError(false)
            ]
        );
        self::assertEquals('number,inRange', $rules->getName());
    }

    public function testGroupOptions(): void
    {
        $rules = new Rules(
            [
                (new Number())->min(10),
                (new InRange([10, 20]))->skipOnError(false)
            ]
        );
        self::assertEquals(
            [
                0 =>
                    [
                        0 => 'number',
                        1 =>
                            [
                                'skipOnEmpty' => false,
                                'skipOnError' => true,
                                'notANumberMessage' => 'Value must be a number.',
                                'asInteger' => false,
                                'min' => 10,
                                'tooSmallMessage' => 'Value must be no less than {min}.',
                                'max' => NULL,
                                'tooBigMessage' => 'Value must be no greater than {max}.',
                            ],
                    ],
                1 =>
                    [
                        0 => 'inRange',
                        1 =>
                            [
                                'skipOnEmpty' => false,
                                'skipOnError' => false,
                                'message' => 'This value is invalid.',
                                'range' =>
                                    [
                                        0 => 10,
                                        1 => 20,
                                    ],
                                'strict' => false,
                                'not' => false,
                            ],
                    ],
            ], $rules->getOptions());
    }
}
