<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use InvalidArgumentException;
use stdClass;
use Yiisoft\Validator\Rule\AnyRule;
use Yiisoft\Validator\Rule\Integer;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Rule\Type\FloatType;
use Yiisoft\Validator\Rule\Type\IntegerType;
use Yiisoft\Validator\Tests\Rule\Base\RuleTestCase;
use Yiisoft\Validator\Tests\Rule\Base\RuleWithOptionsTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\SkipOnErrorTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\WhenTestTrait;

use const PHP_INT_MAX;

final class NumberTest extends RuleTestCase
{
    use RuleWithOptionsTestTrait;
    use SkipOnErrorTestTrait;
    use WhenTestTrait;

    public function testNumberEmptyPattern(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Pattern can\'t be empty.');
        new Number(pattern: '');
    }

    public function testIntegerEmptyPattern(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Pattern can\'t be empty.');
        new Integer(pattern: '');
    }

    public function testGetName(): void
    {
        $rule = new Number();
        $this->assertSame(Number::class, $rule->getName());
    }

    public static function dataOptions(): array
    {
        return [
            'number, default' => [
                new Number(),
                [
                    'min' => null,
                    'max' => null,
                    'incorrectInputMessage' => [
                        'template' => 'The allowed types for {property} are integer, float and string. {type} given.',
                        'parameters' => [],
                    ],
                    'notNumberMessage' => [
                        'template' => '{Property} must be a number.',
                        'parameters' => [],
                    ],
                    'lessThanMinMessage' => [
                        'template' => '{Property} must be no less than {min}.',
                        'parameters' => ['min' => null],
                    ],
                    'greaterThanMaxMessage' => [
                        'template' => '{Property} must be no greater than {max}.',
                        'parameters' => ['max' => null],
                    ],
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                    'pattern' => '/^\s*[-+]?\d*\.?\d+([eE][-+]?\d+)?\s*$/',
                ],
            ],
            'number, custom' => [
                new Number(
                    min: 2,
                    max: 10,
                    incorrectInputMessage: 'Custom message 1.',
                    notNumberMessage: 'Custom message 2.',
                    lessThanMinMessage: 'Custom message 3.',
                    greaterThanMaxMessage: 'Custom message 4.',
                    pattern: 'test',
                    skipOnEmpty: true,
                    skipOnError: true,
                ),
                [
                    'min' => 2,
                    'max' => 10,
                    'incorrectInputMessage' => [
                        'template' => 'Custom message 1.',
                        'parameters' => [],
                    ],
                    'notNumberMessage' => [
                        'template' => 'Custom message 2.',
                        'parameters' => [],
                    ],
                    'lessThanMinMessage' => [
                        'template' => 'Custom message 3.',
                        'parameters' => ['min' => 2],
                    ],
                    'greaterThanMaxMessage' => [
                        'template' => 'Custom message 4.',
                        'parameters' => ['max' => 10],
                    ],
                    'skipOnEmpty' => true,
                    'skipOnError' => true,
                    'pattern' => 'test',
                ],
            ],
            'integer, default ' => [
                new Integer(),
                [
                    'min' => null,
                    'max' => null,
                    'incorrectInputMessage' => [
                        'template' => 'The allowed types for {property} are integer, float and string. {type} given.',
                        'parameters' => [],
                    ],
                    'notNumberMessage' => [
                        'template' => '{Property} must be an integer.',
                        'parameters' => [],
                    ],
                    'lessThanMinMessage' => [
                        'template' => '{Property} must be no less than {min}.',
                        'parameters' => ['min' => null],
                    ],
                    'greaterThanMaxMessage' => [
                        'template' => '{Property} must be no greater than {max}.',
                        'parameters' => ['max' => null],
                    ],
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                    'pattern' => '/^\s*[+-]?\d+\s*$/',
                ],
            ],
            'integer, custom' => [
                new Integer(
                    min: 2,
                    max: 10,
                    incorrectInputMessage: 'Custom message 1.',
                    notNumberMessage: 'Custom message 2.',
                    lessThanMinMessage: 'Custom message 3.',
                    greaterThanMaxMessage: 'Custom message 4.',
                    pattern: 'test',
                    skipOnEmpty: true,
                    skipOnError: true,
                ),
                [
                    'min' => 2,
                    'max' => 10,
                    'incorrectInputMessage' => [
                        'template' => 'Custom message 1.',
                        'parameters' => [],
                    ],
                    'notNumberMessage' => [
                        'template' => 'Custom message 2.',
                        'parameters' => [],
                    ],
                    'lessThanMinMessage' => [
                        'template' => 'Custom message 3.',
                        'parameters' => ['min' => 2],
                    ],
                    'greaterThanMaxMessage' => [
                        'template' => 'Custom message 4.',
                        'parameters' => ['max' => 10],
                    ],
                    'skipOnEmpty' => true,
                    'skipOnError' => true,
                    'pattern' => 'test',
                ],
            ],
        ];
    }

    public static function dataValidationPassed(): array
    {
        return [
            [20, [new Number()]],
            [0, [new Number()]],
            [.5, [new Number()]],
            [-20, [new Number()]],
            ['20', [new Number()]],
            [25.45, [new Number()]],
            ['25,45', [new Number()]],
            ['-1.23', [new Number()]],
            ['-4.423e-12', [new Number()]],
            ['12E3', [new Number()]],

            [20, [new Integer()]],
            [0, [new Integer()]],
            ['20', [new Integer()]],
            ['020', [new Integer()]],
            [0x14, [new Integer()]],
            ['5.5e1', [new Number()]],

            [1, [new Number(min: 1)]],
            [PHP_INT_MAX + 1, [new Number(min: 1)]],

            [1, [new Integer(min: 1)]],

            [1, [new Number(max: 1)]],
            [1, [new Number(max: 1.25)]],
            ['22e-12', [new Number(max: 1.25)]],
            ['125e-2', [new Number(max: 1.25)]],
            [1, [new Integer(max: 1.25)]],

            [0, [new Number(min: -10, max: 20)]],
            [-10, [new Number(min: -10, max: 20)]],

            [0, [new Integer(min: -10, max: 20)]],

            // https://github.com/yiisoft/validator/issues/655
            'limit types with other rules, any: validation passed right away' => [
                1,
                [
                    new AnyRule([new IntegerType(), new FloatType()]),
                    new Number(),
                ],
            ],
            'limit types with other rules, any: validation passed later' => [
                1.5,
                [
                    new AnyRule([new IntegerType(), new FloatType()]),
                    new Number(),
                ],
            ],
        ];
    }

    public static function dataValidationFailed(): array
    {
        $notNumberMessage = 'Value must be a number.';
        $notIntegerMessage = 'Value must be an integer.';

        return [
            [false, [new Number()], ['' => ['The allowed types for value are integer, float and string. bool given.']]],
            [true, [new Number()], ['' => ['The allowed types for value are integer, float and string. bool given.']]],
            [
                [1, 2, 3],
                [new Number()],
                ['' => ['The allowed types for value are integer, float and string. array given.']],
            ],
            [
                new stdClass(),
                [new Number()],
                ['' => ['The allowed types for value are integer, float and string. stdClass given.']],
            ],
            [
                fopen('php://stdin', 'rb'),
                [new Number()],
                ['' => ['The allowed types for value are integer, float and string. resource (stream) given.']],
            ],

            ['12:45', [new Number()], ['' => [$notNumberMessage]]],
            ['e12', [new Number()], ['' => [$notNumberMessage]]],
            ['-e3', [new Number()], ['' => [$notNumberMessage]]],
            ['-4.534-e-12', [new Number()], ['' => [$notNumberMessage]]],
            ['12.23^4', [new Number()], ['' => [$notNumberMessage]]],
            ['43^32', [new Number()], ['' => [$notNumberMessage]]],

            [25.45, [new Integer()], ['' => [$notIntegerMessage]]],
            ['25,45', [new Integer()], ['' => [$notIntegerMessage]]],
            ['0x14', [new Integer()], ['' => [$notIntegerMessage]]],

            ['-1.23', [new Integer()], ['' => [$notIntegerMessage]]],
            ['-4.423e-12', [new Integer()], ['' => [$notIntegerMessage]]],
            ['12E3', [new Integer()], ['' => [$notIntegerMessage]]],
            ['e12', [new Integer()], ['' => [$notIntegerMessage]]],
            ['-e3', [new Integer()], ['' => [$notIntegerMessage]]],
            ['-4.534-e-12', [new Integer()], ['' => [$notIntegerMessage]]],
            ['12.23^4', [new Integer()], ['' => [$notIntegerMessage]]],

            [-1, [new Number(min: 1)], ['' => ['Value must be no less than 1.']]],
            ['22e-12', [new Number(min: 1)], ['' => ['Value must be no less than 1.']]],

            [-1, [new Integer(min: 1)], ['' => ['Value must be no less than 1.']]],
            ['22e-12', [new Integer(min: 1)], ['' => [$notIntegerMessage]]],
            [1.5, [new Number(max: 1.25)], ['' => ['Value must be no greater than 1.25.']]],

            // TODO: fix wrong message
            [1.5, [new Integer(max: 1.25)], ['' => [$notIntegerMessage]]],
            ['22e-12', [new Integer(max: 1.25)], ['' => [$notIntegerMessage]]],
            ['125e-2', [new Integer(max: 1.25)], ['' => [$notIntegerMessage]]],

            [-11, [new Number(min: -10, max: 20)], ['' => ['Value must be no less than -10.']]],
            [21, [new Number(min: -10, max: 20)], ['' => ['Value must be no greater than 20.']]],
            [-11, [new Integer(min: -10, max: 20)], ['' => ['Value must be no less than -10.']]],
            [22, [new Integer(min: -10, max: 20)], ['' => ['Value must be no greater than 20.']]],
            ['20e-1', [new Integer(min: -10, max: 20)], ['' => [$notIntegerMessage]]],
            'custom error' => [
                0,
                [new Number(min: 5, lessThanMinMessage: 'Value is too small.')],
                ['' => ['Value is too small.']],
            ],

            // https://github.com/yiisoft/validator/issues/655
            'limit types with other rules, any: validation failed' => [
                '1.5',
                [
                    new AnyRule([new IntegerType(), new FloatType()]),
                    new Number(),
                ],
                ['' => ['At least one of the inner rules must pass the validation.']],
            ],
        ];
    }

    public function testSkipOnError(): void
    {
        $this->testSkipOnErrorInternal(new Number(), new Number(skipOnError: true));
    }

    public function testWhen(): void
    {
        $when = static fn(mixed $value): bool => $value !== null;
        $this->testWhenInternal(new Number(), new Number(when: $when));
    }
}
