<?php

namespace Yiisoft\Validator\Tests\Rule;

use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\Rule\Length;

/**
 * @group validators
 */
class LengthTest extends TestCase
{


    public function testValidateValue()
    {
        $val = new Length();
        $this->assertFalse($val->validateValue(['not a string'])->isValid());
        $this->assertFalse($val->validateValue(new \stdClass())->isValid());
        $this->assertTrue($val->validateValue('Just some string')->isValid());
        $this->assertFalse($val->validateValue(true)->isValid());
        $this->assertFalse($val->validateValue(false)->isValid());
    }

    public function testValidateValueLength()
    {
        $val = (new Length())
            ->min(25)
            ->max(25);
        $this->assertTrue($val->validateValue(str_repeat('x', 25))->isValid());
        $this->assertTrue($val->validateValue(str_repeat('€', 25))->isValid());
        $this->assertFalse($val->validateValue(str_repeat('x', 125))->isValid());
        $this->assertFalse($val->validateValue('')->isValid());

        $val = (new Length())
            ->min(25);
        $this->assertTrue($val->validateValue(str_repeat('x', 125))->isValid());
        $this->assertTrue($val->validateValue(str_repeat('€', 25))->isValid());
        $this->assertFalse($val->validateValue(str_repeat('x', 13))->isValid());
        $this->assertFalse($val->validateValue('')->isValid());

        $val = (new Length())
            ->max(25);
        $this->assertTrue($val->validateValue(str_repeat('x', 25))->isValid());
        $this->assertTrue($val->validateValue(str_repeat('Ä', 24))->isValid());
        $this->assertfalse($val->validateValue(str_repeat('x', 1250))->isValid());
        $this->assertTrue($val->validateValue('')->isValid());

        $val = (new Length())
            ->min(10)
            ->max(25);
        $this->assertTrue($val->validateValue(str_repeat('x', 15))->isValid());
        $this->assertTrue($val->validateValue(str_repeat('x', 10))->isValid());
        $this->assertTrue($val->validateValue(str_repeat('x', 20))->isValid());
        $this->assertTrue($val->validateValue(str_repeat('x', 25))->isValid());
        $this->assertFalse($val->validateValue(str_repeat('x', 5))->isValid());
        $this->assertFalse($val->validateValue('')->isValid());
    }

    public function testValidateValueMin()
    {
        $rule = (new Length())
            ->min(1);

        $result = $rule->validateValue('');
        $this->assertFalse($result->isValid());
        $this->assertContains('{attribute} should contain at least {min, number} {min, plural, one{character} other{characters}}.', $result->getErrors()[0]);
        $this->assertTrue($rule->validateValue(str_repeat('x', 5))->isValid());
    }


    public function testValidateValueMax()
    {
        $rule = (new Length())
            ->max(100);

        $this->assertTrue($rule->validateValue(str_repeat('x', 5))->isValid());

        $result = $rule->validateValue(str_repeat('x', 1230));
        $this->assertFalse($result->isValid());
        $this->assertContains('{attribute} should contain at most {max, number} {max, plural, one{character} other{characters}}.', $result->getErrors()[0]);
    }
}
