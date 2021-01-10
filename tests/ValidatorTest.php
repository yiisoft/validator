<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests;

use Yiisoft\Validator\DataSetInterface;
use Yiisoft\Validator\Exception\MissingAttributeException;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\Boolean;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\Tests\Stub\CustomUrlRule;
use Yiisoft\Validator\Validator;

class ValidatorTest extends TranslatorMock
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
        $this->expectExceptionMessage('Attribute rules should be either an instance of Rule class or an array of instances of Rule class.');

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
                'bool' => [new Boolean()],
                'int' => [
                    (new Number())->integer(),
                    (new Number())->integer()->min(44),
                    static function ($value): Result {
                        $result = new Result();
                        if ($value !== 42) {
                            $result->addError('Value should be 42!');
                        }
                        return $result;
                    },
                ],
            ]
        );

        $results = $validator->validate($dataObject);

        $this->assertTrue($results->getResult('bool')->isValid());

        $intResult = $results->getResult('int');
        $this->assertFalse($intResult->isValid());
        $this->assertCount(1, $intResult->getErrors());

        $translator = $this->createTranslatorMock([
            'Value must be no less than {min}.' => 'Translate of: Value must be no less than {min}.',
        ]);
        $this->assertEquals('Translate of: Value must be no less than 44.', $intResult->getErrors($translator)[0]);
    }

    public function testAddingRulesViaConstructorAndTranslator(): void
    {
        $dataObject = $this->getDataObject(
            [
                'bool' => true,
                'int' => 41,
            ]
        );

        $validator = new Validator(
            [
                'bool' => [new Boolean()],
                'int' => [
                    (new Number())->integer(),
                    (new Number())->integer()->min(44),
                    static function ($value): Result {
                        $result = new Result();
                        if ($value !== 42) {
                            $result->addError('Value should be 42!');
                        }
                        return $result;
                    },
                ],
            ]
        );

        $results = $validator->validate($dataObject);

        $intResult = $results->getResult('int');

        $translator = $this->createTranslatorMock([
            'Value must be no less than {min}.' => 'Translate of: Value must be no less than {min}.',
        ]);
        $this->assertEquals('Translate of: Value must be no less than 44.', $intResult->getErrors($translator)[0]);
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
                'int' => [
                    (new Number())->integer(),
                    (new Number())->integer()->min(44),
                    static function ($value): Result {
                        $result = new Result();
                        if ($value !== 42) {
                            $result->addError('Value should be 42!');
                        }
                        return $result;
                    },
                ],
            ]
        );

        $this->assertEquals([
            'bool' => [
                [
                    'boolean',
                    'message' => 'The value must be either "1" or "0".',
                    'strict' => false,
                    'trueValue' => '1',
                    'falseValue' => '0',
                    'skipOnEmpty' => false,
                    'skipOnError' => true,
                ],
            ],
            'int' => [
                [
                    'number',
                    'notANumberMessage' => 'Value must be an integer.',
                    'asInteger' => true,
                    'min' => null,
                    'tooSmallMessage' => 'Value must be no less than .',
                    'max' => null,
                    'tooBigMessage' => 'Value must be no greater than .',
                    'skipOnEmpty' => false,
                    'skipOnError' => true,
                ],
                [
                    'number',
                    'notANumberMessage' => 'Value must be an integer.',
                    'asInteger' => true,
                    'min' => 44,
                    'tooSmallMessage' => 'Value must be no less than 44.',
                    'max' => null,
                    'tooBigMessage' => 'Value must be no greater than .',
                    'skipOnEmpty' => false,
                    'skipOnError' => true,
                ],
                [
                    'callback',
                    'skipOnEmpty' => false,
                    'skipOnError' => true,
                ],
            ],
        ], $validator->asArray());
    }

    public function testAsArrayWithGroupRule(): void
    {
        $validator = new Validator(
            [
                'bool' => (new Boolean()),
                'int' => [
                    new Required(),
                    new CustomUrlRule(),
                ],
            ]
        );

        $this->assertEquals([
            'bool' => [
                [
                    'boolean',
                    'message' => 'The value must be either "1" or "0".',
                    'strict' => false,
                    'trueValue' => '1',
                    'falseValue' => '0',
                    'skipOnEmpty' => false,
                    'skipOnError' => true,
                ],
            ],
            'int' => [
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
                        'pattern' => '/^{schemes}:\\/\\/(([A-Z0-9][A-Z0-9_-]*)(\\.[A-Z0-9][A-Z0-9_-]*)+)(?::\\d{1,5})?(?:$|[?\\/#])/i',
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
            ],
        ], $validator->asArray());
    }

    public function testAsArrayWithGroupRuleAndTranslator(): void
    {
        $validator = new Validator(
            [
                'bool' => (new Boolean()),
                'int' => [
                    new Required(),
                    new CustomUrlRule(),
                ],
            ]
        );
        $translator = $this->createTranslatorMock([
            'The value must be either "{true}" or "{false}".' => 'Translate of: The value must be either "{true}" or "{false}".',
            'Value cannot be blank.' => 'Translate of: Value cannot be blank.',
            'This value is not a valid URL.' => 'Translate of: This value is not a valid URL.',
            'This value must be a string.' => 'Translate of: This value must be a string.',
            'This value should contain at least {min, number} {min, plural, one{character} other{characters}}.' => 'Translate of: This value should contain at least {min, number} {min, plural, one{character} other{characters}}.',
            'This value should contain at most {max, number} {max, plural, one{character} other{characters}}.' => 'Translate of: This value should contain at most {max, number} {max, plural, one{character} other{characters}}.',
        ]);

        $this->assertEquals([
            'bool' => [
                [
                    'boolean',
                    'message' => 'Translate of: The value must be either "1" or "0".',
                    'strict' => false,
                    'trueValue' => '1',
                    'falseValue' => '0',
                    'skipOnEmpty' => false,
                    'skipOnError' => true,
                ],
            ],
            'int' => [
                [
                    'required',
                    'message' => 'Translate of: Value cannot be blank.',
                    'skipOnEmpty' => false,
                    'skipOnError' => true,
                ],
                [
                    'customUrlRule',
                    [
                        'required',
                        'message' => 'Translate of: Value cannot be blank.',
                        'skipOnEmpty' => false,
                        'skipOnError' => true,
                    ],
                    [
                        'url',
                        'message' => 'Translate of: This value is not a valid URL.',
                        'enableIDN' => true,
                        'validSchemes' => ['http', 'https',],
                        'pattern' => '/^{schemes}:\\/\\/(([A-Z0-9][A-Z0-9_-]*)(\\.[A-Z0-9][A-Z0-9_-]*)+)(?::\\d{1,5})?(?:$|[?\\/#])/i',
                        'skipOnEmpty' => false,
                        'skipOnError' => true,
                    ],
                    [
                        'hasLength',
                        'message' => 'Translate of: This value must be a string.',
                        'min' => null,
                        'tooShortMessage' => 'Translate of: This value should contain at least {min, number} {min, plural, one{character} other{characters}}.',
                        'max' => 20,
                        'tooLongMessage' => 'Translate of: This value should contain at most {max, number} {max, plural, one{character} other{characters}}.',
                        'encoding' => 'UTF-8',
                        'skipOnEmpty' => false,
                        'skipOnError' => true,
                    ],
                ],
            ],
        ], $validator->asArray($translator));
    }
}
