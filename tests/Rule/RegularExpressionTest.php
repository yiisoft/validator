<?php
namespace Yiisoft\Validator\Tests\Rule;

use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\Rule\RegularExpression;

/**
 * @group validators
 */
class RegularExpressionTest extends TestCase
{
    public function testValidateValue()
    {
        $rule = new RegularExpression('/^[a-zA-Z0-9](\.)?([^\/]*)$/m');
        $this->assertTrue($rule->validateValue('b.4')->isValid());
        $this->assertFalse($rule->validateValue('b./')->isValid());
        $this->assertFalse($rule->validateValue(['a', 'b'])->isValid());

        $rule = (new RegularExpression('/^[a-zA-Z0-9](\.)?([^\/]*)$/m'))->not();
        $this->assertFalse($rule->validateValue('b.4')->isValid());
        $this->assertTrue($rule->validateValue('b./')->isValid());
        $this->assertFalse($rule->validateValue(['a', 'b'])->isValid());
    }

//    public function testValidateAttribute()
//    {
//        $val = new RegularExpression(['pattern' => '/^[a-zA-Z0-9](\.)?([^\/]*)$/m']);
//        $m = FakedValidationModel::createWithAttributes(['attr_reg1' => 'b.4']);
//        $val->validateAttribute($m, 'attr_reg1');
//        $this->assertFalse($m->hasErrors('attr_reg1'));
//        $m->attr_reg1 = 'b./';
//        $val->validateAttribute($m, 'attr_reg1');
//        $this->assertTrue($m->hasErrors('attr_reg1'));
//    }
//
//    public function testMessageSetOnInit()
//    {
//        $val = new RegularExpression(['pattern' => '/^[a-zA-Z0-9](\.)?([^\/]*)$/m']);
//        $this->assertInternalType('string', $val->message);
//    }
}
