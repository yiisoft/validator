<?php

namespace Yiisoft\Validator\Tests\Rule;

use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\DataSetInterface;
use Yiisoft\Validator\Rule\Number;

/**
 * @group validators
 */
class NumberTest extends TestCase
{
    private $commaDecimalLocales = ['fr_FR.UTF-8', 'fr_FR.UTF8', 'fr_FR.utf-8', 'fr_FR.utf8', 'French_France.1252'];
    private $pointDecimalLocales = ['en_US.UTF-8', 'en_US.UTF8', 'en_US.utf-8', 'en_US.utf8', 'English_United States.1252'];
    private $oldLocale;

    private function setCommaDecimalLocale(): void
    {
        if ($this->oldLocale === false) {
            $this->markTestSkipped('Your platform does not support locales.');
        }

        if (setlocale(LC_NUMERIC, $this->commaDecimalLocales) === false) {
            $this->markTestSkipped('Could not set any of required locales: ' . implode(', ', $this->commaDecimalLocales));
        }
    }

    private function setPointDecimalLocale(): void
    {
        if ($this->oldLocale === false) {
            $this->markTestSkipped('Your platform does not support locales.');
        }

        if (setlocale(LC_NUMERIC, $this->pointDecimalLocales) === false) {
            $this->markTestSkipped('Could not set any of required locales: ' . implode(', ', $this->pointDecimalLocales));
        }
    }

    private function restoreLocale(): void
    {
        setlocale(LC_NUMERIC, $this->oldLocale);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->oldLocale = setlocale(LC_NUMERIC, 0);
    }

    public function testvalidateSimple(): void
    {
        $rule = new Number();
        $this->assertTrue($rule->validate(20)->isValid());
        $this->assertTrue($rule->validate(0)->isValid());
        $this->assertTrue($rule->validate(-20)->isValid());
        $this->assertTrue($rule->validate('20')->isValid());
        $this->assertTrue($rule->validate(25.45)->isValid());

        $this->setPointDecimalLocale();
        $this->assertFalse($rule->validate('25,45')->isValid());
        $this->setCommaDecimalLocale();
        $this->assertTrue($rule->validate('25,45')->isValid());
        $this->restoreLocale();

        $this->assertFalse($rule->validate('12:45')->isValid());
    }

    public function testvalidateSimpleInteger(): void
    {
        $rule = (new Number())
            ->integer();

        $this->assertTrue($rule->validate(20)->isValid());
        $this->assertTrue($rule->validate(0)->isValid());
        $this->assertFalse($rule->validate(25.45)->isValid());
        $this->assertTrue($rule->validate('20')->isValid());
        $this->assertFalse($rule->validate('25,45')->isValid());
        $this->assertTrue($rule->validate('020')->isValid());
        $this->assertTrue($rule->validate(0x14)->isValid());
        $this->assertFalse($rule->validate('0x14')->isValid()); // todo check this
    }

    public function testvalidateAdvanced(): void
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

    public function testvalidateAdvancedInteger(): void
    {
        $rule = (new Number())->integer();
        $this->assertFalse($rule->validate('-1.23')->isValid());
        $this->assertFalse($rule->validate('-4.423e-12')->isValid());
        $this->assertFalse($rule->validate('12E3')->isValid());
        $this->assertFalse($rule->validate('e12')->isValid());
        $this->assertFalse($rule->validate('-e3')->isValid());
        $this->assertFalse($rule->validate('-4.534-e-12')->isValid());
        $this->assertFalse($rule->validate('12.23^4')->isValid());
    }

    public function testvalidateWithLocaleWhereDecimalPointIsComma(): void
    {
        $rule = new Number();

        $this->setPointDecimalLocale();
        $this->assertTrue($rule->validate(.5)->isValid());

        $this->setCommaDecimalLocale();
        $this->assertTrue($rule->validate(.5)->isValid());

        $this->restoreLocale();
    }

    public function testvalidateMin(): void
    {
        $rule = (new Number())
            ->min(1);

        $this->assertTrue($rule->validate(1)->isValid());

        $result = $rule->validate(-1);
        $this->assertFalse($result->isValid());
        $this->assertStringContainsString('must be no less than 1.', $result->getErrors()[0]);

        $this->assertFalse($rule->validate('22e-12')->isValid());
        $this->assertTrue($rule->validate(PHP_INT_MAX + 1)->isValid());
    }

    public function testvalidateMinInteger(): void
    {
        $rule = (new Number())
            ->min(1)
            ->integer();

        $this->assertTrue($rule->validate(1)->isValid(), '1 is a valid integer');
        $this->assertFalse($rule->validate(-1)->isValid(), '-1 is not a valid integer');
        $this->assertFalse($rule->validate('22e-12')->isValid(), '22e-12 is not a valid integer');
    }

    public function testvalidateMax(): void
    {
        $rule = (new Number())
            ->max(1.25);

        $this->assertTrue($rule->validate(1)->isValid());
        $this->assertFalse($rule->validate(1.5)->isValid());
        $this->assertTrue($rule->validate('22e-12')->isValid());
        $this->assertTrue($rule->validate('125e-2')->isValid());
    }

    public function testvalidateMaxInteger(): void
    {
        $rule = (new Number())
            ->max(1.25)
            ->integer();

        $this->assertTrue($rule->validate(1)->isValid());
        $this->assertFalse($rule->validate(1.5)->isValid());
        $this->assertFalse($rule->validate('22e-12')->isValid());
        $this->assertFalse($rule->validate('125e-2')->isValid());
    }

    public function testvalidateRange(): void
    {
        $rule = (new Number())
            ->min(-10)
            ->max(20);

        $this->assertTrue($rule->validate(0)->isValid());
        $this->assertTrue($rule->validate(-10)->isValid());
        $this->assertFalse($rule->validate(-11)->isValid());
        $this->assertFalse($rule->validate(21)->isValid());
    }

    public function testvalidateRangeInteger(): void
    {
        $rule = (new Number())
            ->min(-10)
            ->max(20)
            ->integer();

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
        $rule = (new Number())
            ->min(10);

        $result = $rule->validate(10);
        $this->assertTrue($result->isValid());
    }

    public function testLessThanMin(): void
    {
        $rule = (new Number())
            ->min(10);

        $result = $rule->validate(5);
        $this->assertFalse($result->isValid());
    }

    public function testMaxEdge(): void
    {
        $rule = (new Number())
            ->max(10);

        $result = $rule->validate(10);
        $this->assertTrue($result->isValid());
    }

    public function testMaxEdgeInteger(): void
    {
        $rule = (new Number())
            ->max(10)
            ->integer();

        $result = $rule->validate(10);
        $this->assertTrue($result->isValid());
    }

    public function testMoreThanMax(): void
    {
        $rule = (new Number())
            ->max(10);

        $result = $rule->validate(15);
        $this->assertFalse($result->isValid());
    }

    public function testFloatWithInteger(): void
    {
        $rule = (new Number())
            ->max(10)
            ->integer();

        $result = $rule->validate(3.43);
        $this->assertFalse($result->isValid());
    }

    public function testArray(): void
    {
        $rule = (new Number())
            ->min(1);

        $result = $rule->validate([1, 2, 3]);
        $this->assertFalse($result->isValid());
    }

    /**
     * @see https://github.com/yiisoft/yii2/issues/11672
     */
    public function testStdClass(): void
    {
        $rule = (new Number())
            ->min(1);

        $result = $rule->validate(new \stdClass());
        $this->assertFalse($result->isValid());
    }

    public function getDataSet(array $attributeValues): DataSetInterface
    {
        return new class($attributeValues) implements DataSetInterface {
            private $data;

            public function __construct(array $data)
            {
                $this->data = $data;
            }

            public function getValue(string $key)
            {
                if (isset($this->data[$key])) {
                    return $this->data[$key];
                }

                throw new \RuntimeException("There is no $key in the class.");
            }
        };
    }

    public function testEnsureCustomMessageIsSetOnvalidate(): void
    {
        $rule = (new Number())
            ->min(5)
            ->tooSmallMessage('Value is too small.');

        $result = $rule->validate(0);
        $this->assertFalse($result->isValid());
        $this->assertCount(1, $result->getErrors());
        $errors = $result->getErrors();
        $this->assertSame('Value is too small.', $errors[0]);
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

    public function testValidateToString(): void
    {
        $rule = new Number();
        $object = new class('10') {
            public $foo;

            public function __construct($foo)
            {
                $this->foo = $foo;
            }

            public function __toString(): string
            {
                return $this->foo;
            }
        };
        $this->assertTrue($rule->validate($object)->isValid());
    }
}
