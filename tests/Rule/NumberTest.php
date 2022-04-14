<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use PHPUnit\Framework\TestCase;
use stdClass;
use Yiisoft\Validator\Rule\Number;
use function fclose;
use function is_resource;

class NumberTest extends TestCase
{
    public function testMin(): void
    {
        $rule1 = new Number(min: 1);
        $this->assertSame(1, $rule1->getOptions()['min']);

        $rule2 = $rule1->min(2);
        $this->assertSame(2, $rule2->getOptions()['min']);

        $this->assertNotSame($rule1, $rule2);
    }

    public function testMax(): void
    {
        $rule1 = new Number(max: 1);
        $this->assertSame(1, $rule1->getOptions()['max']);

        $rule2 = $rule1->max(2);
        $this->assertSame(2, $rule2->getOptions()['max']);

        $this->assertNotSame($rule1, $rule2);
    }

    public function testTooSmallMessage(): void
    {
        $rule1 = new Number(tooSmallMessage: 'Message 1.');
        $this->assertSame('Message 1.', $rule1->getOptions()['tooSmallMessage']);

        $rule2 = $rule1->tooSmallMessage('Message 2.');
        $this->assertSame('Message 2.', $rule2->getOptions()['tooSmallMessage']);

        $this->assertNotSame($rule1, $rule2);
    }

    public function testTooBigMessage(): void
    {
        $rule1 = new Number(tooBigMessage: 'Message 1.');
        $this->assertSame('Message 1.', $rule1->getOptions()['tooBigMessage']);

        $rule2 = $rule1->tooBigMessage('Message 2.');
        $this->assertSame('Message 2.', $rule2->getOptions()['tooBigMessage']);

        $this->assertNotSame($rule1, $rule2);
    }

    public function testIntegerPattern(): void
    {
        $rule1 = new Number(integerPattern: 'Pattern 1');
        $this->assertSame('Pattern 1', $rule1->getOptions()['integerPattern']);

        $rule2 = $rule1->integerPattern('Pattern 2');
        $this->assertSame('Pattern 2', $rule2->getOptions()['integerPattern']);

        $this->assertNotSame($rule1, $rule2);
    }

    public function testNumberPattern(): void
    {
        $rule1 = new Number(numberPattern: 'Pattern 1');
        $this->assertSame('Pattern 1', $rule1->getOptions()['numberPattern']);

        $rule2 = $rule1->numberPattern('Pattern 2');
        $this->assertSame('Pattern 2', $rule2->getOptions()['numberPattern']);

        $this->assertNotSame($rule1, $rule2);
    }

    public function validateSimpleProvider(): array
    {
        return [
            [20, true],
            [0, true],
            [-20, true],
            ['20', true],
            [25.45, true],
            ['25,45', true],
            ['12:45', false],
        ];
    }

    /**
     * @dataProvider validateSimpleProvider
     */
    public function testValidateSimple(mixed $value, bool $expectedIsValid): void
    {
        $rule = new Number();
        $result = $rule->validate($value);

        $this->assertSame($expectedIsValid, $result->isValid());
    }

    public function validateSimpleIntegerProvider(): array
    {
        return [
            [20, true],
            [0, true],
            [25.45, false],
            ['20', true],
            ['25,45', false],
            ['020', true],
            [0x14, true],
            ['0x14', false], // TODO: Check this
        ];
    }

    /**
     * @dataProvider validateSimpleIntegerProvider
     */
    public function testValidateSimpleInteger(mixed $value, bool $expectedIsValid): void
    {
        $rule = new Number(asInteger: true);
        $result = $rule->validate($value);

        $this->assertSame($expectedIsValid, $result->isValid());
    }

    public function validateBooleanProvider(): array
    {
        return [
            [false],
            [true],
        ];
    }

    /**
     * @dataProvider validateBooleanProvider
     */
    public function testValidateBoolean(mixed $value): void
    {
        $rule = new Number();
        $result = $rule->validate($value);

        $this->assertFalse($result->isValid());
    }

    public function validateAdvancedProvider(): array
    {
        return [
            'signed float' => ['-1.23', true],
            'signed float + exponent' => ['-4.423e-12', true],
            'integer + exponent' => ['12E3', true],
            'just exponent' => ['e12', false],
            'just exponent with minus sign' => ['-e3', false],
            '"signed" exponent' => ['-4.534-e-12', false],
            'expression instead of value' => ['12.23^4', false],
        ];
    }

    /**
     * @dataProvider validateAdvancedProvider
     */
    public function testValidateAdvanced(string $value, bool $expectedIsValid): void
    {
        $rule = new Number();
        $result = $rule->validate($value);

        $this->assertSame($expectedIsValid, $result->isValid());
    }

    public function validateAdvancedInteger(): array
    {
        return [
            ['-1.23'],
            ['-4.423e-12'],
            ['12E3'],
            ['e12'],
            ['-e3'],
            ['-4.534-e-12'],
            ['12.23^4'],
        ];
    }

    /**
     * @dataProvider validateAdvancedInteger
     */
    public function testValidateAdvancedInteger(string $value): void
    {
        $rule = new Number(asInteger: true);
        $result = $rule->validate($value);

        $this->assertFalse($result->isValid());
    }

    public function testValidateWhereDecimalPointIsComma(): void
    {
        $rule = new Number();
        $result = $rule->validate(.5);

        $this->assertTrue($result->isValid());
    }

    public function validateMinProvider(): array
    {
        $rule = new Number(min: 1);

        return [
            [$rule, 1, true, []],
            [$rule, -1, false, ['Value must be no less than 1.']],
            [$rule, '22e-12', false, ['Value must be no less than 1.']],
            [$rule, PHP_INT_MAX + 1, true, []],
        ];
    }

    /**
     * @param string[] $expectedErrorMessages
     *
     * @dataProvider validateMinProvider
     */
    public function testValidateMin(
        Number $rule,
        mixed $value,
        bool $expectedIsValid,
        array $expectedErrorMessages
    ): void {
        $result = $rule->validate($value);

        $this->assertSame($expectedIsValid, $result->isValid());
        $this->assertEquals($expectedErrorMessages, $result->getErrorMessages());
    }

    public function validateMinIntegerProvider(): array
    {
        $rule = new Number(asInteger: true, min: 1);

        return [
            [$rule, 1, true],
            [$rule, -1, false],
            [$rule, '22e-12', false],
        ];
    }

    /**
     * @dataProvider validateMinIntegerProvider
     */
    public function testValidateMinInteger(Number $rule, mixed $value, bool $expectedIsValid): void
    {
        $result = $rule->validate($value);
        $this->assertSame($expectedIsValid, $result->isValid());
    }

    public function validateMaxProvider(): array
    {
        $rule = new Number(max: 1.25);

        return [
            [$rule, 1, true],
            [$rule, 1.5, false],
            [$rule, '22e-12', true],
            [$rule, '125e-2', true],
        ];
    }

    /**
     * @dataProvider validateMaxProvider
     */
    public function testValidateMax(Number $rule, mixed $value, bool $expectedIsValid): void
    {
        $result = $rule->validate($value);
        $this->assertSame($expectedIsValid, $result->isValid());
    }

    public function validateMaxIntegerProvider(): array
    {
        $rule = new Number(asInteger: true, max: 1.25);

        return [
            [$rule, 1, true],
            [$rule, 1.5, false],
            [$rule, '22e-12', false],
            [$rule, '125e-2', false],
        ];
    }

    /**
     * @dataProvider validateMaxIntegerProvider
     */
    public function testValidateMaxInteger(Number $rule, mixed $value, bool $expectedIsValid): void
    {
        $result = $rule->validate($value);
        $this->assertSame($expectedIsValid, $result->isValid());
    }

    public function validateRangeProvider(): array
    {
        $rule = new Number(min: -10, max: 20);

        return [
            [$rule, 0, true],
            [$rule, -10, true],
            [$rule, -11, false],
            [$rule, 21, false],
        ];
    }

    /**
     * @dataProvider validateRangeProvider
     */
    public function testValidateRange(Number $rule, mixed $value, bool $expectedIsValid): void
    {
        $result = $rule->validate($value);
        $this->assertSame($expectedIsValid, $result->isValid());
    }

    public function validateRangeIntegerProvider(): array
    {
        $rule = new Number(asInteger: true, min: -10, max: 20);

        return [
            [$rule, 0, true],
            [$rule, -11, false],
            [$rule, 22, false],
            [$rule, '20e-1', false],
        ];
    }

    /**
     * @dataProvider validateRangeIntegerProvider
     */
    public function testValidateRangeInteger(Number $rule, mixed $value, bool $expectedIsValid): void
    {
        $result = $rule->validate($value);
        $this->assertSame($expectedIsValid, $result->isValid());
    }

    public function testScientificFormat(): void
    {
        $rule = new Number();
        $result = $rule->validate('5.5e1');

        $this->assertTrue($result->isValid());
    }

    public function testExpressionFormat(): void
    {
        $rule = new Number();
        $result = $rule->validate('43^32');

        $this->assertFalse($result->isValid());
    }

    public function testMinEdge(): void
    {
        $rule = new Number(min: 10);
        $result = $rule->validate(10);

        $this->assertTrue($result->isValid());
    }

    public function testLessThanMin(): void
    {
        $rule = new Number(min: 10);
        $result = $rule->validate(5);

        $this->assertFalse($result->isValid());
    }

    public function testMaxEdge(): void
    {
        $rule = new Number(max: 10);
        $result = $rule->validate(10);

        $this->assertTrue($result->isValid());
    }

    public function testMaxEdgeInteger(): void
    {
        $rule = new Number(asInteger: true, min: 10);
        $result = $rule->validate(10);

        $this->assertTrue($result->isValid());
    }

    public function testMoreThanMax(): void
    {
        $rule = new Number(max: 10);
        $result = $rule->validate(15);

        $this->assertFalse($result->isValid());
    }

    public function testFloatWithInteger(): void
    {
        $rule = new Number(asInteger: true, max: 10);
        $result = $rule->validate(3.43);

        $this->assertFalse($result->isValid());
    }

    public function testArray(): void
    {
        $rule = new Number(min: 1);
        $result = $rule->validate([1, 2, 3]);

        $this->assertFalse($result->isValid());
    }

    public function objectProvider(): array
    {
        return [
            [new Number(min: 1), new stdClass()],
            [new Number(), new stdClass()],
        ];
    }

    /**
     * @link https://github.com/yiisoft/yii2/issues/11672
     *
     * @dataProvider objectProvider
     */
    public function testObject(Number $rule, object $value): void
    {
        $result = $rule->validate($value);
        $this->assertFalse($result->isValid());
    }

    public function testEnsureCustomMessageIsSetOnValidate(): void
    {
        $rule = new Number(min: 5, tooSmallMessage: 'Value is too small.');
        $result = $rule->validate(0);

        $this->assertEquals(['Value is too small.'], $result->getErrorMessages());
    }

    public function testValidateResource(): void
    {
        $rule = new Number();
        $fp = fopen('php://stdin', 'rb');
        $result = $rule->validate($fp);

        $this->assertFalse($result->isValid());

        // The check is here for HHVM that was losing handler for unknown reason
        if (is_resource($fp)) {
            fclose($fp);
        }
    }

    public function testGetName(): void
    {
        $rule = new Number();
        $this->assertEquals('number', $rule->getName());
    }

    public function getOptionsProvider(): array
    {
        return [
            [
                new Number(),
                [
                    'asInteger' => false,
                    'min' => null,
                    'max' => null,
                    'notANumberMessage' => 'Value must be a number.',
                    'tooSmallMessage' => 'Value must be no less than .',
                    'tooBigMessage' => 'Value must be no greater than .',
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                    'integerPattern' => '/^\s*[+-]?\d+\s*$/',
                    'numberPattern' => '/^\s*[-+]?\d*\.?\d+([eE][-+]?\d+)?\s*$/',
                ],
            ],
            [
                new Number(min: 1),
                [
                    'asInteger' => false,
                    'min' => 1,
                    'max' => null,
                    'notANumberMessage' => 'Value must be a number.',
                    'tooSmallMessage' => 'Value must be no less than 1.',
                    'tooBigMessage' => 'Value must be no greater than .',
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                    'integerPattern' => '/^\s*[+-]?\d+\s*$/',
                    'numberPattern' => '/^\s*[-+]?\d*\.?\d+([eE][-+]?\d+)?\s*$/',
                ],
            ],
            [
                new Number(max: 1),
                [
                    'asInteger' => false,
                    'min' => null,
                    'max' => 1,
                    'notANumberMessage' => 'Value must be a number.',
                    'tooSmallMessage' => 'Value must be no less than .',
                    'tooBigMessage' => 'Value must be no greater than 1.',
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                    'integerPattern' => '/^\s*[+-]?\d+\s*$/',
                    'numberPattern' => '/^\s*[-+]?\d*\.?\d+([eE][-+]?\d+)?\s*$/',
                ],
            ],
            [
                new Number(min: 2, max: 10),
                [
                    'asInteger' => false,
                    'min' => 2,
                    'max' => 10,
                    'notANumberMessage' => 'Value must be a number.',
                    'tooSmallMessage' => 'Value must be no less than 2.',
                    'tooBigMessage' => 'Value must be no greater than 10.',
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                    'integerPattern' => '/^\s*[+-]?\d+\s*$/',
                    'numberPattern' => '/^\s*[-+]?\d*\.?\d+([eE][-+]?\d+)?\s*$/',
                ],
            ],
            [
                new Number(asInteger: true),
                [
                    'asInteger' => true,
                    'min' => null,
                    'max' => null,
                    'notANumberMessage' => 'Value must be an integer.',
                    'tooSmallMessage' => 'Value must be no less than .',
                    'tooBigMessage' => 'Value must be no greater than .',
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                    'integerPattern' => '/^\s*[+-]?\d+\s*$/',
                    'numberPattern' => '/^\s*[-+]?\d*\.?\d+([eE][-+]?\d+)?\s*$/',
                ],
            ],
        ];
    }

    /**
     * @dataProvider getOptionsProvider
     */
    public function testGetOptions(Number $rule, array $expectedOptions): void
    {
        $this->assertEquals($expectedOptions, $rule->getOptions());
    }
}
