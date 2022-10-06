<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests;

use PHPUnit\Framework\TestCase;
use stdClass;
use Yiisoft\Validator\DataSet\ArrayDataSet;
use Yiisoft\Validator\DataSet\ObjectDataSet;
use Yiisoft\Validator\DataSetInterface;
use Yiisoft\Validator\Error;
use Yiisoft\Validator\Exception\RuleHandlerInterfaceNotImplementedException;
use Yiisoft\Validator\Exception\RuleHandlerNotFoundException;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\Boolean;
use Yiisoft\Validator\Rule\CompareTo;
use Yiisoft\Validator\Rule\HasLength;
use Yiisoft\Validator\Rule\InRange;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\RuleInterface;
use Yiisoft\Validator\SimpleRuleHandlerContainer;
use Yiisoft\Validator\SkipOnEmptyCallback\SkipOnNull;
use Yiisoft\Validator\Tests\Stub\DataSet;
use Yiisoft\Validator\Tests\Stub\FakeValidatorFactory;
use Yiisoft\Validator\Tests\Stub\NotNullRule\NotNull;
use Yiisoft\Validator\Tests\Stub\ObjectWithDataSet;
use Yiisoft\Validator\Tests\Stub\Rule;
use Yiisoft\Validator\Tests\Stub\ObjectWithAttributesOnly;
use Yiisoft\Validator\ValidationContext;
use Yiisoft\Validator\Validator;
use Yiisoft\Validator\ValidatorInterface;

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
                static function (mixed $value): Result {
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
        $result = $validator->validate(
            new DataSet(['property' => '']),
            ['property' => [new Required(when: static fn (mixed $value, ?ValidationContext $context): bool => false)]],
        );

        $this->assertTrue($result->isValid());
    }

    public function testRuleHandlerWithoutImplement(): void
    {
        $ruleHandler = new class () {
        };
        $validator = FakeValidatorFactory::make();

        $this->expectException(RuleHandlerInterfaceNotImplementedException::class);
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

    public function testRuleWithoutHandler(): void
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

    public function requiredDataProvider(): array
    {
        $strictRules = [
            'orderBy' => [new Required()],
            'sort' => [
                new InRange(
                    ['asc', 'desc'],
                    skipOnEmpty: static function (mixed $value, bool $isAttributeMissing): bool {
                        return $isAttributeMissing;
                    }
                ),
            ],
        ];
        $notStrictRules = [
            'orderBy' => [new Required()],
            'sort' => [
                new InRange(
                    ['asc', 'desc'],
                    skipOnEmpty: static function (mixed $value, bool $isAttributeMissing): bool {
                        return $isAttributeMissing || $value === '';
                    }
                ),
            ],
        ];

        return [
            [
                ['merchantId' => [new Required(), new Number(asInteger: true)]],
                new ArrayDataSet(['merchantId' => null]),
                [
                    new Error('Value cannot be blank.', ['merchantId'], ['value' => null]),
                    new Error('Value must be an integer.', ['merchantId'], ['value' => null]),
                ],
            ],
            [
                ['merchantId' => [new Required(), new Number(asInteger: true, skipOnError: true)]],
                new ArrayDataSet(['merchantId' => null]),
                [new Error('Value cannot be blank.', ['merchantId'], ['value' => null])],
            ],
            [
                ['merchantId' => [new Required(), new Number(asInteger: true, skipOnError: true)]],
                new ArrayDataSet(['merchantIdd' => 1]),
                [new Error('Value not passed.', ['merchantId'], ['value' => null])],
            ],

            [
                $strictRules,
                new ArrayDataSet(['orderBy' => 'name', 'sort' => 'asc']),
                [],
            ],
            [
                $notStrictRules,
                new ArrayDataSet(['orderBy' => 'name', 'sort' => 'asc']),
                [],
            ],

            [
                $strictRules,
                new ArrayDataSet(['orderBy' => 'name', 'sort' => 'desc']),
                [],
            ],
            [
                $notStrictRules,
                new ArrayDataSet(['orderBy' => 'name', 'sort' => 'desc']),
                [],
            ],

            [
                $strictRules,
                new ArrayDataSet(['orderBy' => 'name', 'sort' => 'up']),
                [new Error('This value is invalid.', ['sort'], ['value' => 'up'])],
            ],
            [
                $notStrictRules,
                new ArrayDataSet(['orderBy' => 'name', 'sort' => 'up']),
                [new Error('This value is invalid.', ['sort'], ['value' => 'up'])],
            ],

            [
                $strictRules,
                new ArrayDataSet(['orderBy' => 'name', 'sort' => '']),
                [new Error('This value is invalid.', ['sort'], ['value' => ''])],
            ],
            [
                $notStrictRules,
                new ArrayDataSet(['orderBy' => 'name', 'sort' => '']),
                [],
            ],

            [
                $strictRules,
                new ArrayDataSet(['orderBy' => 'name']),
                [],
            ],
            [
                $notStrictRules,
                new ArrayDataSet(['orderBy' => 'name']),
                [],
            ],

            [
                $strictRules,
                new ArrayDataSet(['orderBy' => '']),
                [new Error('Value cannot be blank.', ['orderBy'], ['value' => ''])],
            ],
            [
                $notStrictRules,
                new ArrayDataSet(['orderBy' => '']),
                [new Error('Value cannot be blank.', ['orderBy'], ['value' => ''])],
            ],

            [
                $strictRules,
                new ArrayDataSet([]),
                [new Error('Value not passed.', ['orderBy'], ['value' => null])],
            ],
            [
                $notStrictRules,
                new ArrayDataSet([]),
                [new Error('Value not passed.', ['orderBy'], ['value' => null])],
            ],

            [
                [
                    'name' => [new Required(), new HasLength(min: 3, skipOnError: true)],
                    'description' => [new Required(), new HasLength(min: 5, skipOnError: true)],
                ],
                new ObjectDataSet(
                    new class () {
                        private string $title = '';
                        private string $description = 'abc123';
                    }
                ),
                [new Error('Value not passed.', ['name'], ['value' => null])],
            ],
            [
                null,
                new ObjectDataSet(new ObjectWithDataSet()),
                [],
            ],
        ];
    }

    /**
     * @link https://github.com/yiisoft/validator/issues/173
     * @link https://github.com/yiisoft/validator/issues/289
     * @dataProvider requiredDataProvider
     */
    public function testRequired(array|null $rules, DataSetInterface $dataSet, array $expectedErrors): void
    {
        $validator = FakeValidatorFactory::make();
        $result = $validator->validate($dataSet, $rules);
        $this->assertEquals($expectedErrors, $result->getErrors());
    }

    public function skipOnEmptyDataProvider(): array
    {
        $validator = FakeValidatorFactory::make();
        $rules = [
            'name' => [new HasLength(min: 8)],
            'age' => [new Number(asInteger: true, min: 18)],
        ];
        $stringLessThanMinMessageTemplate = 'This value must contain at least {min, number} {min, plural, one{character} other{characters}}.';
        $intMessageTemplate = 'Value must be an integer.';
        $intLessThanMinMessageTemplate = 'Value must be no less than {min}.';


        $stringLessThanMinMessage = 'This value must contain at least 8 characters.';
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
                    new Error($stringLessThanMinMessage, ['name'], ['min' => 8, 'value' => 'Dmitriy']),
                    new Error($intMessage, ['age'], ['value' => null]),
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
                    new Error($stringLessThanMinMessage, ['name'], ['min' => 8, 'value' => 'Dmitriy']),
                    new Error($intMessage, ['age'], ['value' => null]),
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
                    new Error($stringLessThanMinMessage, ['name'], ['min' => 8, 'value' => 'Dmitriy']),
                    new Error($intLessThanMinMessage, ['age'], ['min' => 18, 'value' => 17]),
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
                    new Error($stringLessThanMinMessage, ['name'], ['min' => 8, 'value' => 'Dmitriy']),
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
                    new Error($stringLessThanMinMessage, ['name'], ['min' => 8, 'value' => 'Dmitriy']),
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
                    new Error($stringLessThanMinMessage, ['name'], ['min' => 8, 'value' => 'Dmitriy']),
                    new Error($intLessThanMinMessage, ['age'], ['min' => 18, 'value' => 17]),
                ],
            ],

            'rule, skipOnEmpty: SkipOnNull, value not passed' => [
                $validator,
                new ArrayDataSet([
                    'name' => 'Dmitriy',
                ]),
                [
                    'name' => [new HasLength(min: 8)],
                    'age' => [new Number(asInteger: true, min: 18, skipOnEmpty: new SkipOnNull())],
                ],
                [
                    new Error($stringLessThanMinMessage, ['name'], ['min' => 8, 'value' => 'Dmitriy']),
                ],
            ],
            'rule, skipOnEmpty: SkipOnNull, value is empty' => [
                $validator,
                new ArrayDataSet([
                    'name' => 'Dmitriy',
                    'age' => null,
                ]),
                [
                    'name' => [new HasLength(min: 8)],
                    'age' => [new Number(asInteger: true, min: 18, skipOnEmpty: new SkipOnNull())],
                ],
                [
                    new Error($stringLessThanMinMessage, ['name'], ['min' => 8, 'value' => 'Dmitriy']),
                ],
            ],
            'rule, skipOnEmpty: SkipOnNull, value is not empty' => [
                $validator,
                new ArrayDataSet([
                    'name' => 'Dmitriy',
                    'age' => 17,
                ]),
                [
                    'name' => [new HasLength(min: 8)],
                    'age' => [new Number(asInteger: true, min: 18, skipOnEmpty: new SkipOnNull())],
                ],
                [
                    new Error($stringLessThanMinMessage, ['name'], ['min' => 8, 'value' => 'Dmitriy']),
                    new Error($intLessThanMinMessage, ['age'], ['min' => 18, 'value' => 17]),
                ],
            ],
            'rule, skipOnEmpty: SkipOnNull, value is not empty (empty string)' => [
                $validator,
                new ArrayDataSet([
                    'name' => 'Dmitriy',
                    'age' => '',
                ]),
                [
                    'name' => [new HasLength(min: 8)],
                    'age' => [new Number(asInteger: true, min: 18, skipOnEmpty: new SkipOnNull())],
                ],
                [
                    new Error($stringLessThanMinMessage, ['name'], ['min' => 8, 'value' => 'Dmitriy']),
                    new Error($intMessage, ['age'], ['value' => null]),
                ],
            ],

            'rule, skipOnEmpty: custom callback, value not passed' => [
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
                            skipOnEmpty: static function (mixed $value, bool $isAttributeMissing): bool {
                                return $value === 0;
                            }
                        ),
                    ],
                ],
                [
                    new Error($stringLessThanMinMessage, ['name'], ['min' => 8, 'value' => 'Dmitriy']),
                    new Error($intMessage, ['age'], ['value' => null]),
                ],
            ],
            'rule, skipOnEmpty: custom callback, value is empty' => [
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
                            skipOnEmpty: static function (mixed $value, bool $isAttributeMissing): bool {
                                return $value === 0;
                            }
                        ),
                    ],
                ],
                [
                    new Error($stringLessThanMinMessage, ['name'], ['min' => 8, 'value' => 'Dmitriy']),
                ],
            ],
            'rule, skipOnEmpty, custom callback, value is not empty' => [
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
                            skipOnEmpty: static function (mixed $value, bool $isAttributeMissing): bool {
                                return $value === 0;
                            }
                        ),
                    ],
                ],
                [
                    new Error($stringLessThanMinMessage, ['name'], ['min' => 8, 'value' => 'Dmitriy']),
                    new Error($intLessThanMinMessage, ['age'], ['min' => 18, 'value' => 17]),
                ],
            ],
            'rule, skipOnEmpty, custom callback, value is not empty (null)' => [
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
                            skipOnEmpty: static function (mixed $value, bool $isAttributeMissing): bool {
                                return $value === 0;
                            }
                        ),
                    ],
                ],
                [
                    new Error($stringLessThanMinMessage, ['name'], ['min' => 8, 'value' => 'Dmitriy']),
                    new Error($intMessage, ['age'], ['value' => null]),
                ],
            ],

            'validator, skipOnEmpty: true, value not passed' => [
                new Validator(new SimpleRuleHandlerContainer(), defaultSkipOnEmpty: true),
                new ArrayDataSet([
                    'name' => 'Dmitriy',
                ]),
                $rules,
                [
                    new Error($stringLessThanMinMessageTemplate, ['name'], ['min' => 8, 'value' => 'Dmitriy']),
                ],
            ],
            'validator, skipOnEmpty: true, value is empty' => [
                new Validator(new SimpleRuleHandlerContainer(), defaultSkipOnEmpty: true),
                new ArrayDataSet([
                    'name' => 'Dmitriy',
                    'age' => null,
                ]),
                $rules,
                [
                    new Error($stringLessThanMinMessageTemplate, ['name'], ['min' => 8, 'value' => 'Dmitriy']),
                ],
            ],
            'validator, skipOnEmpty: true, value is not empty' => [
                new Validator(new SimpleRuleHandlerContainer(), defaultSkipOnEmpty: true),
                new ArrayDataSet([
                    'name' => 'Dmitriy',
                    'age' => 17,
                ]),
                $rules,
                [
                    new Error($stringLessThanMinMessageTemplate, ['name'], ['min' => 8, 'value' => 'Dmitriy']),
                    new Error($intLessThanMinMessageTemplate, ['age'], ['min' => 18, 'value' => 17]),
                ],
            ],

            'validator, skipOnEmpty: SkipOnNull, value not passed' => [
                new Validator(new SimpleRuleHandlerContainer(), defaultSkipOnEmpty: new SkipOnNull()),
                new ArrayDataSet([
                    'name' => 'Dmitriy',
                ]),
                $rules,
                [
                    new Error($stringLessThanMinMessageTemplate, ['name'], ['min' => 8, 'value' => 'Dmitriy']),
                ],
            ],
            'validator, skipOnEmpty: SkipOnNull, value is empty' => [
                new Validator(new SimpleRuleHandlerContainer(), defaultSkipOnEmpty: new SkipOnNull()),
                new ArrayDataSet([
                    'name' => 'Dmitriy',
                    'age' => null,
                ]),
                $rules,
                [
                    new Error($stringLessThanMinMessageTemplate, ['name'], ['min' => 8, 'value' => 'Dmitriy']),
                ],
            ],
            'validator, skipOnEmpty: SkipOnNull, value is not empty' => [
                new Validator(new SimpleRuleHandlerContainer(), defaultSkipOnEmpty: new SkipOnNull()),
                new ArrayDataSet([
                    'name' => 'Dmitriy',
                    'age' => 17,
                ]),
                $rules,
                [
                    new Error($stringLessThanMinMessageTemplate, ['name'], ['min' => 8, 'value' => 'Dmitriy']),
                    new Error($intLessThanMinMessageTemplate, ['age'], ['min' => 18, 'value' => 17]),
                ],
            ],
            'validator, skipOnEmpty: SkipOnNull, value is not empty (empty string)' => [
                new Validator(new SimpleRuleHandlerContainer(), defaultSkipOnEmpty: new SkipOnNull()),
                new ArrayDataSet([
                    'name' => 'Dmitriy',
                    'age' => '',
                ]),
                $rules,
                [
                    new Error($stringLessThanMinMessageTemplate, ['name'], ['min' => 8, 'value' => 'Dmitriy']),
                    new Error($intMessageTemplate, ['age'], ['value' => null]),
                ],
            ],

            'validator, skipOnEmpty: custom callback, value not passed' => [
                new Validator(
                    new SimpleRuleHandlerContainer(),
                    defaultSkipOnEmpty: static function (mixed $value, bool $isAttributeMissing): bool {
                        return $value === 0;
                    }
                ),
                new ArrayDataSet([
                    'name' => 'Dmitriy',
                ]),
                $rules,
                [
                    new Error($stringLessThanMinMessageTemplate, ['name'], ['min' => 8, 'value' => 'Dmitriy']),
                    new Error($intMessageTemplate, ['age'], ['value' => null]),
                ],
            ],
            'validator, skipOnEmpty: custom callback, value is empty' => [
                new Validator(
                    new SimpleRuleHandlerContainer(),
                    defaultSkipOnEmpty: static function (mixed $value, bool $isAttributeMissing): bool {
                        return $value === 0;
                    }
                ),
                new ArrayDataSet([
                    'name' => 'Dmitriy',
                    'age' => 0,
                ]),
                $rules,
                [
                    new Error($stringLessThanMinMessageTemplate, ['name'], ['min' => 8, 'value' => 'Dmitriy']),
                ],
            ],
            'validator, skipOnEmpty: custom callback, value is not empty' => [
                new Validator(
                    new SimpleRuleHandlerContainer(),
                    defaultSkipOnEmpty: static function (mixed $value, bool $isAttributeMissing): bool {
                        return $value === 0;
                    }
                ),
                new ArrayDataSet([
                    'name' => 'Dmitriy',
                    'age' => 17,
                ]),
                $rules,
                [
                    new Error($stringLessThanMinMessageTemplate, ['name'], ['min' => 8, 'value' => 'Dmitriy']),
                    new Error($intLessThanMinMessageTemplate, ['age'], ['min' => 18, 'value' => 17]),
                ],
            ],
            'validator, skipOnEmpty: custom callback, value is not empty (null)' => [
                new Validator(
                    new SimpleRuleHandlerContainer(),
                    defaultSkipOnEmpty: static function (mixed $value, bool $isAttributeMissing): bool {
                        return $value === 0;
                    }
                ),
                new ArrayDataSet([
                    'name' => 'Dmitriy',
                    'age' => null,
                ]),
                $rules,
                [
                    new Error($stringLessThanMinMessageTemplate, ['name'], ['min' => 8, 'value' => 'Dmitriy']),
                    new Error($intMessageTemplate, ['age'], ['value' => null]),
                ],
            ],
        ];
    }

    /**
     * @param Rule[] $rules
     * @param Error[] $expectedErrors
     *
     * @dataProvider skipOnEmptyDataProvider
     */
    public function testSkipOnEmpty(ValidatorInterface $validator, ArrayDataSet $data, array $rules, array $expectedErrors): void
    {
        $result = $validator->validate($data, $rules);
        $this->assertEquals($expectedErrors, $result->getErrors());
    }

    public function initSkipOnEmptyDataProvider(): array
    {
        return [
            'null' => [
                null,
                new class () {
                    #[Required]
                    public ?string $name = null;
                },
                false,
            ],
            'false' => [
                false,
                new class () {
                    #[Required]
                    public ?string $name = null;
                },
                false,
            ],
            'true' => [
                true,
                new class () {
                    #[Required]
                    public ?string $name = null;
                },
                true,
            ],
            'callable' => [
                new SkipOnNull(),
                new class () {
                    #[Required]
                    public ?string $name = null;
                },
                true,
            ],
            'do-not-override-rule' => [
                false,
                new class () {
                    #[Required(skipOnEmpty: true)]
                    public string $name = '';
                },
                true,
            ],
        ];
    }

    /**
     * @dataProvider initSkipOnEmptyDataProvider
     */
    public function testInitSkipOnEmpty(
        bool|callable|null $skipOnEmpty,
        mixed $data,
        bool $expectedResult,
    ): void {
        $validator = new Validator(new SimpleRuleHandlerContainer(), defaultSkipOnEmpty: $skipOnEmpty);

        $result = $validator->validate($data);

        $this->assertSame($expectedResult, $result->isValid());
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

    public function testRuleWithoutSkipOnEmpty(): void
    {
        $validator = new Validator(new SimpleRuleHandlerContainer(), defaultSkipOnEmpty: new SkipOnNull());

        $data = new class () {
            #[NotNull]
            public ?string $name = null;
        };

        $result = $validator->validate($data);

        $this->assertFalse($result->isValid());
    }
}
