<?php
namespace Yiisoft\Validator\Tests\Rule;

use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\Rule\Range;

/**
 * @group validators
 */
class RangeTest extends TestCase
{
    public function testInitException()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('The "range" property must be set.');
        new Range('not an array');
    }

    public function testValidateValue()
    {
        $val = new Range(range(1, 10, 1));
        $this->assertTrue($val->validateValue(1)->isValid());
        $this->assertFalse($val->validateValue(0)->isValid());
        $this->assertFalse($val->validateValue(11)->isValid());
        $this->assertFalse($val->validateValue(5.5)->isValid());
        $this->assertTrue($val->validateValue(10)->isValid());
        $this->assertTrue($val->validateValue('10')->isValid());
        $this->assertTrue($val->validateValue('5')->isValid());
    }

    public function testValidateValueEmpty()
    {
        $rule = (new Range(range(10, 20, 1)))->skipOnEmpty(false);
        $this->assertFalse($rule->validateValue(null)->isValid()); //row RangeValidatorTest.php:101
        $this->assertFalse($rule->validateValue('0')->isValid());
        $this->assertFalse($rule->validateValue(0)->isValid());
        $this->assertFalse($rule->validateValue('')->isValid());

        $rule = (new Range(range(10, 20, 1)))
            ->skipOnEmpty(false)
            ->allowArray(true);
        
        $this->assertTrue($rule->validateValue([])->isValid());
    }

    public function testValidateArrayValue()
    {
        $rule = (new Range(range(1, 10, 1)))
            ->allowArray(true);
        
        $this->assertTrue($rule->validateValue([1, 2, 3, 4, 5])->isValid());
        $this->assertTrue($rule->validateValue([6, 7, 8, 9, 10])->isValid());
        $this->assertFalse($rule->validateValue([0, 1, 2])->isValid());
        $this->assertFalse($rule->validateValue([10, 11, 12])->isValid());
        $this->assertTrue($rule->validateValue(['1', '2', '3', 4, 5, 6])->isValid());
    }

    public function testValidateValueStrict()
    {
        $rule = (new Range(range(1, 10, 1)))
            ->strict();

        $this->assertTrue($rule->validateValue(1)->isValid());
        $this->assertTrue($rule->validateValue(5)->isValid());
        $this->assertTrue($rule->validateValue(10)->isValid());
        $this->assertFalse($rule->validateValue('1')->isValid());
        $this->assertFalse($rule->validateValue('10')->isValid());
        $this->assertFalse($rule->validateValue('5.5')->isValid());
    }

    public function testValidateArrayValueStrict()
    {
        $rule = (new Range(range(1, 10, 1)))
            ->strict()
            ->allowArray(true);

        $this->assertFalse($rule->validateValue(['1', '2', '3', '4', '5', '6'])->isValid());
        $this->assertFalse($rule->validateValue(['1', '2', '3', 4, 5, 6])->isValid());
    }

    public function testValidateValueNot()
    {
        $rule = (new Range(range(1, 10, 1)))
            ->not();
        
        $this->assertFalse($rule->validateValue(1)->isValid());
        $this->assertTrue($rule->validateValue(0)->isValid());
        $this->assertTrue($rule->validateValue(11)->isValid());
        $this->assertTrue($rule->validateValue(5.5)->isValid());
        $this->assertFalse($rule->validateValue(10)->isValid());
        $this->assertFalse($rule->validateValue('10')->isValid());
        $this->assertFalse($rule->validateValue('5')->isValid());
    }

//    public function testValidateAttribute()
//    {
//        $val = new Range(['range' => range(1, 10, 1)]);
//        $m = FakedValidationModel::createWithAttributes(['attr_r1' => 5, 'attr_r2' => 999]);
//        $val->validateAttribute($m, 'attr_r1');
//        $this->assertFalse($m->hasErrors());
//        $val->validateAttribute($m, 'attr_r2');
//        $this->assertTrue($m->hasErrors('attr_r2'));
//        $err = $m->getErrors('attr_r2');
//        $this->assertNotFalse(stripos($err[0], 'attr_r2'));
//    }

    public function testValidateSubsetArrayable()
    {
        // Test in array, values are arrays. IE: ['a'] in [['a'], ['b']]
        $rule = (new Range([['a'], ['b']]))
            ->allowArray(false);
        
        $this->assertTrue($rule->validateValue(['a'])->isValid());

        // Test in array, values are arrays. IE: ['a', 'b'] subset [['a', 'b', 'c']
        $rule = (new Range(['a', 'b', 'c']))
            ->allowArray(true);

        $this->assertTrue($rule->validateValue(['a', 'b'])->isValid());

        // Test in array, values are arrays. IE: ['a', 'b'] subset [['a', 'b', 'c']
        $rule = (new Range(['a', 'b', 'c']))
            ->allowArray(true);

        $this->assertTrue($rule->validateValue(new \ArrayObject(['a', 'b']))->isValid());


        // Test range as ArrayObject.
        $rule = (new Range(new \ArrayObject(['a', 'b'])))
            ->allowArray(false);

        $this->assertTrue($rule->validateValue('a')->isValid());
    }
}
