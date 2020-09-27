<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests;

use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\DataSetInterface;
use Yiisoft\Validator\Exception\MissingAttributeException;
use Yiisoft\Validator\Error;
use Yiisoft\Validator\Rule\Boolean;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\Rules;
use Yiisoft\Validator\Tests\Stub\CustomUrlRule;
use Yiisoft\Validator\Validator;

class ValidatorTest extends TestCase
{
    public function getDataObject(array $attributes): DataSetInterface
    {
        return new class($attributes) implements DataSetInterface {
            private array $data;

            public function __construct(array $data)
            {
                $this->data = $data;
            }

            public function getAttributeValue(string $attribute)
            {
                if (!$this->hasAttribute($attribute)) {
                    throw new MissingAttributeException("There is no \"$attribute\" attribute in the class.");
                }

                return $this->data[$attribute];
            }

            public function hasAttribute(string $attribute): bool
            {
                return isset($this->data[$attribute]);
            }
        };
    }

    public function testThrowExceptionWithInvalidRule(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Attribute rules should be either an instance of RuleInterface.');

        $validator = new Validator(
            [
                'bool' => 'Invalid argument',
            ]
        );
    }

    public function testAddingRulesViaConstructor(): void
    {
        $dataObject = $this->getDataObject(
            [
                'bool' => true,
                'int' => 41,
            ]
        );

        $validator = new Validator(
            [
                'bool' => new Boolean(),
                'int' => new Rules([
                    (new Number())->integer(),
                    (new Number())->integer()->min(44),
                    static function ($value): Error {
                        $result = new Error();
                        if ($value !== 42) {
                            $result->addError('Value should be 42!');
                        }
                        return $result;
                    }
                ]),
            ]
        );

        $results = $validator->validate($dataObject);

        $this->assertTrue($results->getResult('bool')->isValid());

        $intResult = $results->getResult('int');
        $this->assertFalse($intResult->isValid());
        $this->assertCount(1, $intResult->getErrors());
    }

    public function testAddingRulesOneByOne(): void
    {
        $dataObject = $this->getDataObject(
            [
                'bool' => true,
                'int' => 42,
            ]
        );

        $validator = new Validator();
        $validator->addRule('bool', new Boolean());
        $validator->addRule('int', (new Number())->integer());
        $validator->addRule('int', (new Number())->integer()->min(44));

        $results = $validator->validate($dataObject);

        $this->assertTrue($results->getResult('bool')->isValid());

        $intResult = $results->getResult('int');
        $this->assertFalse($intResult->isValid());
        $this->assertCount(1, $intResult->getErrors());
    }

    public function testAsArray(): void
    {
        $validator = new Validator(
            [
                'bool' => (new Boolean()),
                'int' => new Rules([
                    (new Number())->integer(),
                    (new Number())->integer()->min(44),
                    static function ($value): Error {
                        $result = new Error();
                        if ($value !== 42) {
                            $result->addError('Value should be 42!');
                        }
                        return $result;
                    }
                ]),
            ]
        );

        $this->assertEquals([
            'bool' =>
                [
                    0 => 'boolean',
                    1 =>
                        [
                            'skipOnEmpty' => false,
                            'skipOnError' => true,
                            'strict' => false,
                            'trueValue' => '1',
                            'falseValue' => '0',
                            'message' => 'The value must be either "{true}" or "{false}".',
                        ],
                ],
            'int' =>
                [
                    0 => 'number',
                    1 =>
                        [
                            'skipOnEmpty' => false,
                            'skipOnError' => true,
                            'notANumberMessage' => 'Value must be an integer.',
                            'asInteger' => true,
                            'min' => 44,
                            'tooSmallMessage' => 'Value must be no less than {min}.',
                            'max' => NULL,
                            'tooBigMessage' => 'Value must be no greater than {max}.',
                        ],
                ],
        ], $validator->asArray());
    }

    public function testAsArrayWithGroupRule(): void
    {
        $validator = new Validator(
            [
                'bool' => (new Boolean()),
                'int' => new Rules([
                    new Required(),
                    new CustomUrlRule()
                ]),
            ]
        );

        $this->assertEquals([
            'bool' =>
                [
                    0 => 'boolean',
                    1 =>
                        [
                            'skipOnEmpty' => false,
                            'skipOnError' => true,
                            'strict' => false,
                            'trueValue' => '1',
                            'falseValue' => '0',
                            'message' => 'The value must be either "{true}" or "{false}".',
                        ],
                ],
            'int' =>
                [
                    0 => 'customUrlRule',
                    1 =>
                        [
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
                                    0 => 'url',
                                    1 =>
                                        [
                                            'skipOnEmpty' => false,
                                            'skipOnError' => true,
                                            'message' => 'This value is not a valid URL.',
                                            'enableIDN' => true,
                                            'validSchemes' =>
                                                [
                                                    0 => 'http',
                                                    1 => 'https',
                                                ],
                                            'pattern' => '/^{schemes}:\\/\\/(([A-Z0-9][A-Z0-9_-]*)(\\.[A-Z0-9][A-Z0-9_-]*)+)(?::\\d{1,5})?(?:$|[?\\/#])/i',
                                        ],
                                ],
                            2 =>
                                [
                                    0 => 'hasLength',
                                    1 =>
                                        [
                                            'skipOnEmpty' => false,
                                            'skipOnError' => true,
                                            'message' => 'This value must be a string.',
                                            'min' => NULL,
                                            'tooShortMessage' => 'This value should contain at least {min, number} {min, plural, one{character} other{characters}}.',
                                            'max' => 20,
                                            'tooLongMessage' => 'This value should contain at most {max, number} {max, plural, one{character} other{characters}}.',
                                            'encoding' => 'UTF-8',
                                        ],
                                ],
                        ],
                ],
        ], $validator->asArray());
    }
}
