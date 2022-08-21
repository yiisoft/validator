<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests;

use PHPUnit\Framework\TestCase;
use stdClass;
use Yiisoft\Validator\DataSet\ArrayDataSet;
use Yiisoft\Validator\Error;
use Yiisoft\Validator\Exception\RuleHandlerInterfaceNotImplementedException;
use Yiisoft\Validator\Exception\RuleHandlerNotFoundException;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\Boolean;
use Yiisoft\Validator\Rule\CompareTo;
use Yiisoft\Validator\Rule\HasLength;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\RuleInterface;
use Yiisoft\Validator\SimpleRuleHandlerContainer;
use Yiisoft\Validator\SkipOnEmptyCallback\SkipNone;
use Yiisoft\Validator\SkipOnEmptyCallback\SkipOnEmpty;
use Yiisoft\Validator\SkipOnEmptyCallback\SkipOnNull;
use Yiisoft\Validator\Tests\Stub\DataSet;
use Yiisoft\Validator\Tests\Stub\FakeValidatorFactory;
use Yiisoft\Validator\Tests\Stub\Rule;
use Yiisoft\Validator\Tests\Stub\ObjectWithAttributesOnly;
use Yiisoft\Validator\ValidationContext;
use Yiisoft\Validator\Validator;

class ValidatorTest extends TestCase
{
    public function testAddingRulesViaConstructor(): void
    {
        $dataObject = new DataSet(['bool' => true, 'int' => 41]);
        $validator = FakeValidatorFactory::make();
        $result = $validator->validate($dataObject, [
            'bool' => [new Boolean()],
            'int' => [
                new Number(asInteger: true),
                new Number(asInteger: true, min: 44),
                static function ($value): Result {
                    $result = new Result();
                    if ($value !== 42) {
                        $result->addError('Value should be 42!', ['int']);
                    }

                    return $result;
                },
            ],
        ]);

        $this->assertTrue($result->isAttributeValid('bool'));
        $this->assertFalse($result->isAttributeValid('int'));
    }

    public function diverseTypesDataProvider(): array
    {
        $class = new stdClass();
        $class->property = true;

        return [
            'object' => [$class],
            'true' => [true],
            'non-empty-string' => ['true'],
            'integer' => [12345],
            'float' => [12.345],
            'false' => [false],
        ];
    }

    /**
     * @dataProvider diverseTypesDataProvider
     */
    public function testDiverseTypes($dataSet): void
    {
        $validator = FakeValidatorFactory::make();
        $result = $validator->validate($dataSet, [new Required()]);

        $this->assertTrue($result->isValid());
    }

    public function testNullAsDataSet(): void
    {
        $validator = FakeValidatorFactory::make();
        $result = $validator->validate(null, ['property' => [new CompareTo(null)]]);

        $this->assertTrue($result->isValid());
    }

    public function testPreValidation(): void
    {
        $validator = FakeValidatorFactory::make();
        $result = $validator->validate(new DataSet(['property' => '']), [
            'property' => [
                new Required(
                    when: static function (mixed $value, ?ValidationContext $context): bool {
                        return false;
                    },
                ),
            ],
        ]);

        $this->assertTrue($result->isValid());
    }

    public function testRuleHandlerWithoutImplement()
    {
        $this->expectException(RuleHandlerInterfaceNotImplementedException::class);

        $ruleHandler = new class () {
        };
        $validator = FakeValidatorFactory::make();
        $validator->validate(new DataSet(['property' => '']), [
            'property' => [
                new class ($ruleHandler) implements RuleInterface {
                    public function __construct(private $ruleHandler)
                    {
                    }

                    public function getName(): string
                    {
                        return 'test';
                    }

                    public function getHandlerClassName(): string
                    {
                        return $this->ruleHandler::class;
                    }
                },
            ],
        ]);
    }

    public function testRuleWithoutHandler()
    {
        $this->expectException(RuleHandlerNotFoundException::class);

        $validator = FakeValidatorFactory::make();
        $validator->validate(new DataSet(['property' => '']), [
            'property' => [
                new class () implements RuleInterface {
                    public function getName(): string
                    {
                        return 'test';
                    }

                    public function getHandlerClassName(): string
                    {
                        return 'NonExistClass';
                    }
                },
            ],
        ]);
    }

    /**
     * @link https://github.com/yiisoft/validator/issues/173
     */
    public function testMissingRequiredAttribute(): void
    {
        $validator = FakeValidatorFactory::make();
        $dataSet = new ArrayDataSet([
            'merchantIdd' => 1,
        ]);
        $rules = [
            'merchantId' => [new Required(), new Number(asInteger: true)],
        ];
        $result = $validator->validate($dataSet, $rules);
        $this->assertEquals([
            new Error('Value cannot be blank.', ['merchantId']),
            new Error('Value must be an integer.', ['merchantId']),
        ], $result->getErrors());
    }

    public function skipOnEmptyDataProvider(): array
    {
        $validator = new Validator(new SimpleRuleHandlerContainer());
        $rules = [
            'name' => [new HasLength(min: 8)],
            'age' => [new Number(asInteger: true, min: 18)],
        ];
        $stringLessThanMinMessage = 'This value must contain at least {min, number} {min, plural, one{character} ' .
            'other{characters}}.';
        $intMessage = 'Value must be an integer.';
        $intLessThanMinMessage = 'Value must be no less than 18.';

        return [
            'rule / validator, skipOnEmpty: false, value not passed' => [
                $validator,
                new ArrayDataSet([
                    'name' => 'Dmitriy',
                ]),
                $rules,
                [
                    new Error($stringLessThanMinMessage, ['name']),
                    new Error($intMessage, ['age']),
                ],
            ],
            'rule / validator, skipOnEmpty: false, value is empty' => [
                $validator,
                new ArrayDataSet([
                    'name' => 'Dmitriy',
                    'age' => null,
                ]),
                $rules,
                [
                    new Error($stringLessThanMinMessage, ['name']),
                    new Error($intMessage, ['age']),
                ],
            ],
            'rule / validator, skipOnEmpty: false, value is not empty' => [
                $validator,
                new ArrayDataSet([
                    'name' => 'Dmitriy',
                    'age' => 17,
                ]),
                $rules,
                [
                    new Error($stringLessThanMinMessage, ['name']),
                    new Error($intLessThanMinMessage, ['age']),
                ],
            ],

            'rule, skipOnEmpty: true, value not passed' => [
                $validator,
                new ArrayDataSet([
                    'name' => 'Dmitriy',
                ]),
                [
                    'name' => [new HasLength(min: 8)],
                    'age' => [new Number(asInteger: true, min: 18, skipOnEmpty: true)],
                ],
                [
                    new Error($stringLessThanMinMessage, ['name']),
                ],
            ],
            'rule, skipOnEmpty: true, value is empty' => [
                $validator,
                new ArrayDataSet([
                    'name' => 'Dmitriy',
                    'age' => null,
                ]),
                [
                    'name' => [new HasLength(min: 8)],
                    'age' => [new Number(asInteger: true, min: 18, skipOnEmpty: true)],
                ],
                [
                    new Error($stringLessThanMinMessage, ['name']),
                ],
            ],
            'rule, skipOnEmpty: true, value is not empty' => [
                $validator,
                new ArrayDataSet([
                    'name' => 'Dmitriy',
                    'age' => 17,
                ]),
                [
                    'name' => [new HasLength(min: 8)],
                    'age' => [new Number(asInteger: true, min: 18, skipOnEmpty: true)],
                ],
                [
                    new Error($stringLessThanMinMessage, ['name']),
                    new Error($intLessThanMinMessage, ['age']),
                ],
            ],

            'rule, skipOnEmptyCallback, SkipOnNull, value not passed' => [
                $validator,
                new ArrayDataSet([
                    'name' => 'Dmitriy',
                ]),
                [
                    'name' => [new HasLength(min: 8)],
                    'age' => [new Number(asInteger: true, min: 18, skipOnEmptyCallback: new SkipOnNull())],
                ],
                [
                    new Error($stringLessThanMinMessage, ['name']),
                ],
            ],
            'rule, skipOnEmptyCallback, SkipOnNull, value is empty' => [
                $validator,
                new ArrayDataSet([
                    'name' => 'Dmitriy',
                    'age' => null,
                ]),
                [
                    'name' => [new HasLength(min: 8)],
                    'age' => [new Number(asInteger: true, min: 18, skipOnEmptyCallback: new SkipOnNull())],
                ],
                [
                    new Error($stringLessThanMinMessage, ['name']),
                ],
            ],
            'rule, skipOnEmptyCallback, SkipOnNull, value is not empty' => [
                $validator,
                new ArrayDataSet([
                    'name' => 'Dmitriy',
                    'age' => 17,
                ]),
                [
                    'name' => [new HasLength(min: 8)],
                    'age' => [new Number(asInteger: true, min: 18, skipOnEmptyCallback: new SkipOnNull())],
                ],
                [
                    new Error($stringLessThanMinMessage, ['name']),
                    new Error($intLessThanMinMessage, ['age']),
                ],
            ],
            'rule, skipOnEmptyCallback, SkipOnNull, value is not empty (empty string)' => [
                $validator,
                new ArrayDataSet([
                    'name' => 'Dmitriy',
                    'age' => '',
                ]),
                [
                    'name' => [new HasLength(min: 8)],
                    'age' => [new Number(asInteger: true, min: 18, skipOnEmptyCallback: new SkipOnNull())],
                ],
                [
                    new Error($stringLessThanMinMessage, ['name']),
                    new Error($intMessage, ['age']),
                ],
            ],

            'rule, skipOnEmptyCallback, custom, value not passed' => [
                $validator,
                new ArrayDataSet([
                    'name' => 'Dmitriy',
                ]),
                [
                    'name' => [new HasLength(min: 8)],
                    'age' => [
                        new Number(
                            asInteger: true,
                            min: 18,
                            skipOnEmptyCallback: static function (mixed $value): bool {
                                return $value === 0;
                            }
                        ),
                    ],
                ],
                [
                    new Error($stringLessThanMinMessage, ['name']),
                    new Error($intMessage, ['age']),
                ],
            ],
            'rule, skipOnEmptyCallback, custom, value is empty' => [
                $validator,
                new ArrayDataSet([
                    'name' => 'Dmitriy',
                    'age' => 0,
                ]),
                [
                    'name' => [new HasLength(min: 8)],
                    'age' => [
                        new Number(
                            asInteger: true,
                            min: 18,
                            skipOnEmptyCallback: static function (mixed $value): bool {
                                return $value === 0;
                            }
                        ),
                    ],
                ],
                [
                    new Error($stringLessThanMinMessage, ['name']),
                ],
            ],
            'rule, skipOnEmptyCallback, custom, value is not empty' => [
                $validator,
                new ArrayDataSet([
                    'name' => 'Dmitriy',
                    'age' => 17,
                ]),
                [
                    'name' => [new HasLength(min: 8)],
                    'age' => [
                        new Number(
                            asInteger: true,
                            min: 18,
                            skipOnEmptyCallback: static function (mixed $value): bool {
                                return $value === 0;
                            }
                        ),
                    ],
                ],
                [
                    new Error($stringLessThanMinMessage, ['name']),
                    new Error($intLessThanMinMessage, ['age']),
                ],
            ],
            'rule, skipOnEmptyCallback, custom, value is not empty (null)' => [
                $validator,
                new ArrayDataSet([
                    'name' => 'Dmitriy',
                    'age' => null,
                ]),
                [
                    'name' => [new HasLength(min: 8)],
                    'age' => [
                        new Number(
                            asInteger: true,
                            min: 18,
                            skipOnEmptyCallback: static function (mixed $value): bool {
                                return $value === 0;
                            }
                        ),
                    ],
                ],
                [
                    new Error($stringLessThanMinMessage, ['name']),
                    new Error($intMessage, ['age']),
                ],
            ],

            'validator, skipOnEmpty: true, value not passed' => [
                new Validator(new SimpleRuleHandlerContainer(), skipOnEmpty: true),
                new ArrayDataSet([
                    'name' => 'Dmitriy',
                ]),
                $rules,
                [
                    new Error($stringLessThanMinMessage, ['name']),
                ],
            ],
            'validator, skipOnEmpty: true, value is empty' => [
                new Validator(new SimpleRuleHandlerContainer(), skipOnEmpty: true),
                new ArrayDataSet([
                    'name' => 'Dmitriy',
                    'age' => null,
                ]),
                $rules,
                [
                    new Error($stringLessThanMinMessage, ['name']),
                ],
            ],
            'validator, skipOnEmpty: true, value is not empty' => [
                new Validator(new SimpleRuleHandlerContainer(), skipOnEmpty: true),
                new ArrayDataSet([
                    'name' => 'Dmitriy',
                    'age' => 17,
                ]),
                $rules,
                [
                    new Error($stringLessThanMinMessage, ['name']),
                    new Error($intLessThanMinMessage, ['age']),
                ],
            ],

            'validator, skipOnEmptyCallback, SkipOnNull, value not passed' => [
                new Validator(new SimpleRuleHandlerContainer(), skipOnEmptyCallback: new SkipOnNull()),
                new ArrayDataSet([
                    'name' => 'Dmitriy',
                ]),
                $rules,
                [
                    new Error($stringLessThanMinMessage, ['name']),
                ],
            ],
            'validator, skipOnEmptyCallback, SkipOnNull, value is empty' => [
                new Validator(new SimpleRuleHandlerContainer(), skipOnEmptyCallback: new SkipOnNull()),
                new ArrayDataSet([
                    'name' => 'Dmitriy',
                    'age' => null,
                ]),
                $rules,
                [
                    new Error($stringLessThanMinMessage, ['name']),
                ],
            ],
            'validator, skipOnEmptyCallback, SkipOnNull, value is not empty' => [
                new Validator(new SimpleRuleHandlerContainer(), skipOnEmptyCallback: new SkipOnNull()),
                new ArrayDataSet([
                    'name' => 'Dmitriy',
                    'age' => 17,
                ]),
                $rules,
                [
                    new Error($stringLessThanMinMessage, ['name']),
                    new Error($intLessThanMinMessage, ['age']),
                ],
            ],
            'validator, skipOnEmptyCallback, SkipOnNull, value is not empty (empty string)' => [
                new Validator(new SimpleRuleHandlerContainer(), skipOnEmptyCallback: new SkipOnNull()),
                new ArrayDataSet([
                    'name' => 'Dmitriy',
                    'age' => '',
                ]),
                $rules,
                [
                    new Error($stringLessThanMinMessage, ['name']),
                    new Error($intMessage, ['age']),
                ],
            ],

            'validator, skipOnEmptyCallback, custom, value not passed' => [
                new Validator(
                    new SimpleRuleHandlerContainer(),
                    skipOnEmptyCallback: static function (mixed $value): bool {
                        return $value === 0;
                    }
                ),
                new ArrayDataSet([
                    'name' => 'Dmitriy',
                ]),
                $rules,
                [
                    new Error($stringLessThanMinMessage, ['name']),
                    new Error($intMessage, ['age']),
                ],
            ],
            'validator, skipOnEmptyCallback, custom, value is empty' => [
                new Validator(
                    new SimpleRuleHandlerContainer(),
                    skipOnEmptyCallback: static function (mixed $value): bool {
                        return $value === 0;
                    }
                ),
                new ArrayDataSet([
                    'name' => 'Dmitriy',
                    'age' => 0,
                ]),
                $rules,
                [
                    new Error($stringLessThanMinMessage, ['name']),
                ],
            ],
            'validator, skipOnEmptyCallback, custom, value is not empty' => [
                new Validator(
                    new SimpleRuleHandlerContainer(),
                    skipOnEmptyCallback: static function (mixed $value): bool {
                        return $value === 0;
                    }
                ),
                new ArrayDataSet([
                    'name' => 'Dmitriy',
                    'age' => 17,
                ]),
                $rules,
                [
                    new Error($stringLessThanMinMessage, ['name']),
                    new Error($intLessThanMinMessage, ['age']),
                ],
            ],
            'validator, skipOnEmptyCallback, custom, value is not empty (null)' => [
                new Validator(
                    new SimpleRuleHandlerContainer(),
                    skipOnEmptyCallback: static function (mixed $value): bool {
                        return $value === 0;
                    }
                ),
                new ArrayDataSet([
                    'name' => 'Dmitriy',
                    'age' => null,
                ]),
                $rules,
                [
                    new Error($stringLessThanMinMessage, ['name']),
                    new Error($intMessage, ['age']),
                ],
            ],
        ];
    }

    /**
     * @param Validator $validator
     * @param ArrayDataSet $data
     * @param Rule[] $rules
     * @param Error[] $expectedErrors
     *
     * @dataProvider skipOnEmptyDataProvider
     */
    public function testSkipOnEmpty(Validator $validator, ArrayDataSet $data, array $rules, array $expectedErrors): void
    {
        $result = $validator->validate($data, $rules);
        $this->assertEquals($expectedErrors, $result->getErrors());
    }

    public function initSkipOnEmptyDataProvider(): array
    {
        return [
            [new Validator(new SimpleRuleHandlerContainer()), null, null],
            [new Validator(new SimpleRuleHandlerContainer(), skipOnEmpty: false), false, SkipNone::class],
            [new Validator(new SimpleRuleHandlerContainer(), skipOnEmpty: true), true, SkipOnEmpty::class],
            [
                new Validator(new SimpleRuleHandlerContainer(), skipOnEmptyCallback: new SkipOnNull()),
                true,
                SkipOnNull::class,
            ],
        ];
    }

    /**
     * @dataProvider initSkipOnEmptyDataProvider
     */
    public function testInitSkipOnEmpty(
        Validator $validator,
        ?bool $expectedSkipOnEmpty,
        ?string $expectedSkipOnEmptyCallbackClass
    ): void {
        $this->assertSame($expectedSkipOnEmpty, $validator->getSkipOnEmpty());

        if ($expectedSkipOnEmptyCallbackClass !== null) {
            $this->assertInstanceOf($expectedSkipOnEmptyCallbackClass, $validator->getSkipOnEmptyCallback());
        }
    }

    public function testObjectWithAttributesOnly(): void
    {
        $object = new ObjectWithAttributesOnly();

        $validator = FakeValidatorFactory::make();

        $result = $validator->validate($object);

        $this->assertFalse($result->isValid());
        $this->assertCount(1, $result->getErrorMessages());
        $this->assertStringStartsWith('This value must contain at least', $result->getErrorMessages()[0]);
    }
}
