<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\DataSetInterface;
use Yiisoft\Validator\Exception\MissingAttributeException;
use Yiisoft\Validator\Rule;
use Yiisoft\Validator\Rule\Number;

/**
 * @group validators
 */
class NumberTest extends TestCase
{
    public function testValidateSimple(): void
    {
        $rule = new Number();
        $this->assertTrue($rule->validate(20)->isValid());
        $this->assertTrue($rule->validate(0)->isValid());
        $this->assertTrue($rule->validate(-20)->isValid());
        $this->assertTrue($rule->validate('20')->isValid());
        $this->assertTrue($rule->validate(25.45)->isValid());
        $this->assertTrue($rule->validate('25,45')->isValid());
        $this->assertFalse($rule->validate('12:45')->isValid());
    }

    public function testValidateSimpleInteger(): void
    {
        $rule = new Number(asInteger: true);

        $this->assertTrue($rule->validate(20)->isValid());
        $this->assertTrue($rule->validate(0)->isValid());
        $this->assertFalse($rule->validate(25.45)->isValid());
        $this->assertTrue($rule->validate('20')->isValid());
        $this->assertFalse($rule->validate('25,45')->isValid());
        $this->assertTrue($rule->validate('020')->isValid());
        $this->assertTrue($rule->validate(0x14)->isValid());
        $this->assertFalse($rule->validate('0x14')->isValid()); // todo check this
    }

    public function testValidateBoolean(): void
    {
        $rule = new Number();

        $this->assertFalse($rule->validate(false)->isValid());
        $this->assertFalse($rule->validate(true)->isValid());
    }

    public function testValidateAdvanced(): void
    {
        $rule = new Number();
        $this->assertTrue($rule->validate('-1.23')->isValid()); // signed float
        $this->assertTrue($rule->validate('-4.423e-12')->isValid()); // signed float + exponent
        $this->assertTrue($rule->validate('12E3')->isValid()); // integer + exponent
        $this->assertFalse($rule->validate('e12')->isValid()); // just exponent
        $this->assertFalse($rule->validate('-e3')->isValid());
        $this->assertFalse($rule->validate('-4.534-e-12')->isValid()); // 'signed' exponent
        $this->assertFalse($rule->validate('12.23^4')->isValid()); // expression instead of value
    }

    public function testValidateAdvancedInteger(): void
    {
        $rule = new Number(asInteger: true);
        $this->assertFalse($rule->validate('-1.23')->isValid());
        $this->assertFalse($rule->validate('-4.423e-12')->isValid());
        $this->assertFalse($rule->validate('12E3')->isValid());
        $this->assertFalse($rule->validate('e12')->isValid());
        $this->assertFalse($rule->validate('-e3')->isValid());
        $this->assertFalse($rule->validate('-4.534-e-12')->isValid());
        $this->assertFalse($rule->validate('12.23^4')->isValid());
    }

    public function testValidateWhereDecimalPointIsComma(): void
    {
        $rule = new Number();
        $this->assertTrue($rule->validate(.5)->isValid());
    }

    public function testValidateMin(): void
    {
        $rule = new Number(min: 1);

        $this->assertTrue($rule->validate(1)->isValid());

        $result = $rule->validate(-1);
        $this->assertFalse($result->isValid());
        $this->assertEquals(['Value must be no less than 1.'], $result->getErrorMessages());

        $this->assertFalse($rule->validate('22e-12')->isValid());
        $this->assertTrue($rule->validate(PHP_INT_MAX + 1)->isValid());
    }

    public function testValidateMinInteger(): void
    {
        $rule = new Number(asInteger: true, min: 1);

        $this->assertTrue($rule->validate(1)->isValid(), '1 is a valid integer');
        $this->assertFalse($rule->validate(-1)->isValid(), '-1 is not a valid integer');
        $this->assertFalse($rule->validate('22e-12')->isValid(), '22e-12 is not a valid integer');
    }

    public function testValidateMax(): void
    {
        $rule = new Number(max: 1.25);

        $this->assertTrue($rule->validate(1)->isValid());
        $this->assertFalse($rule->validate(1.5)->isValid());
        $this->assertTrue($rule->validate('22e-12')->isValid());
        $this->assertTrue($rule->validate('125e-2')->isValid());
    }

    public function testValidateMaxInteger(): void
    {
        $rule = new Number(asInteger: true, max: 1.25);

        $this->assertTrue($rule->validate(1)->isValid());
        $this->assertFalse($rule->validate(1.5)->isValid());
        $this->assertFalse($rule->validate('22e-12')->isValid());
        $this->assertFalse($rule->validate('125e-2')->isValid());
    }

    public function testValidateRange(): void
    {
        $rule = new Number(min: -10, max: 20);

        $this->assertTrue($rule->validate(0)->isValid());
        $this->assertTrue($rule->validate(-10)->isValid());
        $this->assertFalse($rule->validate(-11)->isValid());
        $this->assertFalse($rule->validate(21)->isValid());
    }

    public function testValidateRangeInteger(): void
    {
        $rule = new Number(asInteger: true, min: -10, max: 20);

        $this->assertTrue($rule->validate(0)->isValid());
        $this->assertFalse($rule->validate(-11)->isValid());
        $this->assertFalse($rule->validate(22)->isValid());
        $this->assertFalse($rule->validate('20e-1')->isValid());
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

    /**
     * @see https://github.com/yiisoft/yii2/issues/11672
     */
    public function testStdClass(): void
    {
        $rule = new Number(min: 1);

        $result = $rule->validate(new \stdClass());
        $this->assertFalse($result->isValid());
    }

    public function getDataSet(array $attributeValues): DataSetInterface
    {
        return new class ($attributeValues) implements DataSetInterface {
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

    public function testEnsureCustomMessageIsSetOnvalidate(): void
    {
        $rule = new Number(min: 5, tooSmallMessage: 'Value is too small.');
        $result = $rule->validate(0);

        $this->assertFalse($result->isValid());
        $this->assertEquals(['Value is too small.'], $result->getErrorMessages());
    }

    public function testValidateObject(): void
    {
        $rule = new Number();
        $value = new \stdClass();
        $this->assertFalse($rule->validate($value)->isValid());
    }

    public function testValidateResource(): void
    {
        $rule = new Number();
        $fp = fopen('php://stdin', 'r');
        $this->assertFalse($rule->validate($fp)->isValid());

        $result = $rule->validate($fp);
        $this->assertFalse($result->isValid());

        // the check is here for HHVM that
        // was losing handler for unknown reason
        if (is_resource($fp)) {
            fclose($fp);
        }
    }

    public function testName(): void
    {
        $this->assertEquals('number', (new Number())->getName());
    }

    public function optionsProvider(): array
    {
        return [
            [
                new Number(),
                [
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                    'asInteger' => false,
                    'min' => null,
                    'max' => null,
                    'notANumberMessage' => 'Value must be a number.',
                    'tooSmallMessage' => 'Value must be no less than .',
                    'tooBigMessage' => 'Value must be no greater than .',
                ],
            ],
            [
                new Number(min: 1),
                [
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                    'asInteger' => false,
                    'min' => 1,
                    'max' => null,
                    'notANumberMessage' => 'Value must be a number.',
                    'tooSmallMessage' => 'Value must be no less than 1.',
                    'tooBigMessage' => 'Value must be no greater than .',
                ],
            ],
            [
                new Number(max: 1),
                [
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                    'asInteger' => false,
                    'min' => null,
                    'max' => 1,
                    'notANumberMessage' => 'Value must be a number.',
                    'tooSmallMessage' => 'Value must be no less than .',
                    'tooBigMessage' => 'Value must be no greater than 1.',
                ],
            ],
            [
                new Number(min: 2, max: 10),
                [
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                    'asInteger' => false,
                    'min' => 2,
                    'max' => 10,
                    'notANumberMessage' => 'Value must be a number.',
                    'tooSmallMessage' => 'Value must be no less than 2.',
                    'tooBigMessage' => 'Value must be no greater than 10.',
                ],
            ],
            [
                new Number(asInteger: true),
                [
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                    'asInteger' => true,
                    'min' => null,
                    'max' => null,
                    'notANumberMessage' => 'Value must be an integer.',
                    'tooSmallMessage' => 'Value must be no less than .',
                    'tooBigMessage' => 'Value must be no greater than .',
                ],
            ],
        ];
    }

    /**
     * @dataProvider optionsProvider
     *
     * @param Rule $rule
     * @param array $expected
     */
    public function testOptions(Rule $rule, array $expected): void
    {
        $this->assertEquals($expected, $rule->getOptions());
    }
}
