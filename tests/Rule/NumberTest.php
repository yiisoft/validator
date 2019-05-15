<?php

namespace Yiisoft\Validator\Tests\Rule;

use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\DataSet;
use Yiisoft\Validator\Rule\Number;

/**
 * @group validators
 */
class NumberTest extends TestCase
{
    private $commaDecimalLocales = ['fr_FR.UTF-8', 'fr_FR.UTF8', 'fr_FR.utf-8', 'fr_FR.utf8', 'French_France.1252'];
    private $pointDecimalLocales = ['en_US.UTF-8', 'en_US.UTF8', 'en_US.utf-8', 'en_US.utf8', 'English_United States.1252'];
    private $oldLocale;

    private function setCommaDecimalLocale()
    {
        if ($this->oldLocale === false) {
            $this->markTestSkipped('Your platform does not support locales.');
        }

        if (setlocale(LC_NUMERIC, $this->commaDecimalLocales) === false) {
            $this->markTestSkipped('Could not set any of required locales: ' . implode(', ', $this->commaDecimalLocales));
        }
    }

    private function setPointDecimalLocale()
    {
        if ($this->oldLocale === false) {
            $this->markTestSkipped('Your platform does not support locales.');
        }

        if (setlocale(LC_NUMERIC, $this->pointDecimalLocales) === false) {
            $this->markTestSkipped('Could not set any of required locales: ' . implode(', ', $this->pointDecimalLocales));
        }
    }

    private function restoreLocale()
    {
        setlocale(LC_NUMERIC, $this->oldLocale);
    }

    protected function setUp()
    {
        parent::setUp();

        $this->oldLocale = setlocale(LC_NUMERIC, 0);
    }

//    public function testEnsureMessageOnInit()
//    {
//        $rule = new Number();
//        $this->assertInternalType('string', $rule->message);
//        $this->assertTrue($rule->max === null);
//        $rule = new Number(['min' => -1, 'max' => 20, 'integerOnly' => true]);
//        $this->assertInternalType('string', $rule->message);
//        $this->assertInternalType('string', $rule->tooSmall);
//        $this->assertInternalType('string', $rule->tooBig);
//    }

    public function testValidateValueSimple()
    {
        $rule = new Number();
        $this->assertTrue($rule->validateValue(20)->isValid());
        $this->assertTrue($rule->validateValue(0)->isValid());
        $this->assertTrue($rule->validateValue(-20)->isValid());
        $this->assertTrue($rule->validateValue('20')->isValid());
        $this->assertTrue($rule->validateValue(25.45)->isValid());

        $this->setPointDecimalLocale();
        $this->assertFalse($rule->validateValue('25,45')->isValid());
        $this->setCommaDecimalLocale();
        $this->assertTrue($rule->validateValue('25,45')->isValid());
        $this->restoreLocale();

        $this->assertFalse($rule->validateValue('12:45')->isValid());
    }

    public function testValidateValueSimpleInteger()
    {
        $rule = (new Number())
            ->integer();

        $this->assertTrue($rule->validateValue(20)->isValid());
        $this->assertTrue($rule->validateValue(0)->isValid());
        $this->assertFalse($rule->validateValue(25.45)->isValid());
        $this->assertTrue($rule->validateValue('20')->isValid());
        $this->assertFalse($rule->validateValue('25,45')->isValid());
        $this->assertTrue($rule->validateValue('020')->isValid());
        $this->assertTrue($rule->validateValue(0x14)->isValid());
        $this->assertFalse($rule->validateValue('0x14')->isValid()); // todo check this
    }

    public function testValidateValueAdvanced()
    {
        $rule = new Number();
        $this->assertTrue($rule->validateValue('-1.23')->isValid()); // signed float
        $this->assertTrue($rule->validateValue('-4.423e-12')->isValid()); // signed float + exponent
        $this->assertTrue($rule->validateValue('12E3')->isValid()); // integer + exponent
        $this->assertFalse($rule->validateValue('e12')->isValid()); // just exponent
        $this->assertFalse($rule->validateValue('-e3')->isValid());
        $this->assertFalse($rule->validateValue('-4.534-e-12')->isValid()); // 'signed' exponent
        $this->assertFalse($rule->validateValue('12.23^4')->isValid()); // expression instead of value
    }

    public function testValidateValueAdvancedInteger()
    {
        $rule = (new Number())->integer();
        $this->assertFalse($rule->validateValue('-1.23')->isValid());
        $this->assertFalse($rule->validateValue('-4.423e-12')->isValid());
        $this->assertFalse($rule->validateValue('12E3')->isValid());
        $this->assertFalse($rule->validateValue('e12')->isValid());
        $this->assertFalse($rule->validateValue('-e3')->isValid());
        $this->assertFalse($rule->validateValue('-4.534-e-12')->isValid());
        $this->assertFalse($rule->validateValue('12.23^4')->isValid());
    }

    public function testValidateValueWithLocaleWhereDecimalPointIsComma()
    {
        $rule = new Number();

        $this->setPointDecimalLocale();
        $this->assertTrue($rule->validateValue(.5)->isValid());

        $this->setCommaDecimalLocale();
        $this->assertTrue($rule->validateValue(.5)->isValid());

        $this->restoreLocale();
    }

    public function testValidateValueMin()
    {
        $rule = (new Number())
            ->min(1);

        $this->assertTrue($rule->validateValue(1)->isValid());

        $result = $rule->validateValue(-1);
        $this->assertFalse($result->isValid());
        $this->assertContains('the input value must be no less than 1.', $result->getErrors()[0]);

        $this->assertFalse($rule->validateValue('22e-12')->isValid());
        $this->assertTrue($rule->validateValue(PHP_INT_MAX + 1)->isValid());
    }

    public function testValidateValueMinInteger()
    {
        $rule = (new Number())
            ->min(1)
            ->integer();

        $this->assertTrue($rule->validateValue(1)->isValid());
        $this->assertFalse($rule->validateValue(-1)->isValid());
        $this->assertFalse($rule->validateValue('22e-12')->isValid());
        $this->assertTrue($rule->validateValue(PHP_INT_MAX + 1)->isValid());
    }

    public function testValidateValueMax()
    {
        $rule = (new Number())
            ->max(1.25);

        $this->assertTrue($rule->validateValue(1)->isValid());
        $this->assertFalse($rule->validateValue(1.5)->isValid());
        $this->assertTrue($rule->validateValue('22e-12')->isValid());
        $this->assertTrue($rule->validateValue('125e-2')->isValid());
    }

    public function testValidateValueMaxInteger()
    {
        $rule = (new Number())
            ->max(1.25)
            ->integer();

        $this->assertTrue($rule->validateValue(1)->isValid());
        $this->assertFalse($rule->validateValue(1.5)->isValid());
        $this->assertFalse($rule->validateValue('22e-12')->isValid());
        $this->assertFalse($rule->validateValue('125e-2')->isValid());
    }

    public function testValidateValueRange()
    {
        $rule = (new Number())
            ->min(-10)
            ->max(20);

        $this->assertTrue($rule->validateValue(0)->isValid());
        $this->assertTrue($rule->validateValue(-10)->isValid());
        $this->assertFalse($rule->validateValue(-11)->isValid());
        $this->assertFalse($rule->validateValue(21)->isValid());
    }

    public function testValidateValueRangeInteger()
    {
        $rule = (new Number())
            ->min(-10)
            ->max(20)
            ->integer();

        $this->assertTrue($rule->validateValue(0)->isValid());
        $this->assertFalse($rule->validateValue(-11)->isValid());
        $this->assertFalse($rule->validateValue(22)->isValid());
        $this->assertFalse($rule->validateValue('20e-1')->isValid());
    }

    public function testScientificFormat()
    {
        $rule = new Number();

        $model = $this->getDataSet([
            'attr_number' => '5.5e1',
        ]);

        $result = $rule->validateAttribute($model, 'attr_number');
        $this->assertTrue($result->isValid());
    }

    public function testExpressionFormat()
    {
        $rule = new Number();

        $model = $this->getDataSet([
            'attr_number' => '43^32', //expression
        ]);

        $result = $rule->validateAttribute($model, 'attr_number');
        $this->assertFalse($result->isValid());
    }

    public function testMinEdgeWith()
    {
        $rule = (new Number())
            ->min(10);

        $model = $this->getDataSet([
            'attr_number' => 10,
        ]);

        $result = $rule->validateAttribute($model, 'attr_number');
        $this->assertTrue($result->isValid());
    }

    public function testLessThanMin()
    {
        $rule = (new Number())
            ->min(10);

        $model = $this->getDataSet([
            'attr_number' => 5,
        ]);
        $result = $rule->validateAttribute($model, 'attr_number');
        $this->assertFalse($result->isValid());
    }

    public function testMaxEdge()
    {
        $rule = (new Number())
            ->max(10);

        $model = $this->getDataSet([
            'attr_number' => 10,
        ]);

        $result = $rule->validateAttribute($model, 'attr_number');
        $this->assertTrue($result->isValid());
    }

    public function testMaxEdgeInteger()
    {
        $rule = (new Number())
            ->max(10)
            ->integer();

        $model = $this->getDataSet([
            'attr_number' => 10,
        ]);

        $result = $rule->validateAttribute($model, 'attr_number');
        $this->assertTrue($result->isValid());
    }

    public function testMoreThanMax()
    {
        $rule = (new Number())
            ->max(10);

        $model = $this->getDataSet([
            'attr_number' => 15,
        ]);

        $result = $rule->validateAttribute($model, 'attr_number');
        $this->assertFalse($result->isValid());
    }

    public function testFloatWithInteger()
    {
        $rule = (new Number())
            ->max(10)
            ->integer();

        $model = $this->getDataSet([
            'attr_number' => 3.43,
        ]);

        $result = $rule->validateAttribute($model, 'attr_number');
        $this->assertFalse($result->isValid());
    }

    public function testArray()
    {
        $rule = (new Number())
            ->min(1);

        $model = $this->getDataSet([
            'attr_num' => [1, 2, 3]
        ]);

        $result = $rule->validateAttribute($model, 'attr_num');
        $this->assertFalse($result->isValid());
    }

    public function testStdClass()
    {
        $rule = (new Number())
            ->min(1);

        // @see https://github.com/yiisoft/yii2/issues/11672
        $model = $this->getDataSet([
            'attr_number' => new \stdClass(),
        ]);

        $result = $rule->validateAttribute($model, 'attr_number');
        $this->assertFalse($result->isValid());
    }

    public function testValidateAttributeWithLocaleWhereDecimalPointIsComma()
    {
        $rule = new Number();

        $model = $this->getDataSet([
            'attr_number' => 0.5,
        ]);

        $this->setPointDecimalLocale();
        $result = $rule->validateAttribute($model, 'attr_number');
        $this->assertTrue($result->isValid());

        $this->setCommaDecimalLocale();
        $result = $rule->validateAttribute($model, 'attr_number');
        $this->assertTrue($result->isValid());

        $this->restoreLocale();
    }

    public function getDataSet(array $attributeValues): DataSet
    {
        return new class ($attributeValues) implements DataSet
        {
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

    public function testEnsureCustomMessageIsSetOnValidateAttribute()
    {
        $rule = (new Number())
            ->min(5)
            ->tooSmallMessage('{attribute} is to small.');

        $model = $this->getDataSet([
            'attr_number' => 0,
        ]);

        $result = $rule->validateAttribute($model, 'attr_number');
        $this->assertFalse($result->isValid());
        $this->assertCount(1, $result->getErrors());
        $errors = $result->getErrors();
        $this->assertSame('attr_number is to small.', $errors[0]);
    }

    public function testValidateObject()
    {
        $rule = new Number();
        $ruleue = new \stdClass();
        $this->assertFalse($rule->validateValue($ruleue)->isValid());
    }

    public function testValidateResource()
    {
        $rule = new Number();
        $fp = fopen('php://stdin', 'r');
        $this->assertFalse($rule->validateValue($fp)->isValid());

        $model = $this->getDataSet([
            'attr_number' => $fp,
        ]);
        $result = $rule->validateAttribute($model, 'attr_number');
        $this->assertFalse($result->isValid());

        // the check is here for HHVM that
        // was losing handler for unknown reason
        if (is_resource($fp)) {
            fclose($fp);
        }
    }

    public function testValidateToString()
    {
        $rule = new Number();
        $object = new class('10')
        {
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
        $this->assertTrue($rule->validateValue($object)->isValid());

        $model = $this->getDataSet([
            'attr_number' => $object,
        ]);

        $result = $rule->validateAttribute($model, 'attr_number');
        $this->assertTrue($result->isValid());
    }
}
