<?php
namespace Yii\Validator\Tests\Rule;

use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\Rule;
use Yiisoft\Validator\Rule\Boolean;


/**
 * @group validators
 */
class BooleanTest extends TestCase
{

    public function validateValueProvider()
    {
        return [
            [new Boolean(), true, true],
            [new Boolean(), false, true],

            [new Boolean(), '0', true],
            [new Boolean(), '1', true],
            [new Boolean(), '5', false],

            [new Boolean(), null, false],
            [new Boolean(), [], false],

            [(new Boolean())->strict(true), '0', true],
            [(new Boolean())->strict(true), '1', true],

            [(new Boolean())->strict(true), true, false],
            [(new Boolean())->strict(true), false, false],

            [(new Boolean())->strict(true)->trueValue(true)->falseValue(false), '0', false],
            [(new Boolean())->strict(true)->trueValue(true)->falseValue(false), [], false],
            [(new Boolean())->strict(true)->trueValue(true)->falseValue(false), true, true],
            [(new Boolean())->strict(true)->trueValue(true)->falseValue(false), false, true],
        ];
    }

    /**
     * @dataProvider validateValueProvider
     */
    public function testValidateValue(Rule $rule, $value, $expected)
    {
        $this->assertSame($expected, $rule->validateValue($value)->isValid());
    }

//    public function testValidateAttributeAndError()
//    {
//        $obj = new FakedValidationModel();
//        $obj->attrA = true;
//        $obj->attrB = '1';
//        $obj->attrC = '0';
//        $obj->attrD = [];
//        $val = new Boolean();
//        $val->validateAttribute($obj, 'attrA');
//        $this->assertFalse($obj->hasErrors('attrA'));
//        $val->validateAttribute($obj, 'attrC');
//        $this->assertFalse($obj->hasErrors('attrC'));
//        $val->strict = true;
//        $val->validateAttribute($obj, 'attrB');
//        $this->assertFalse($obj->hasErrors('attrB'));
//        $val->validateAttribute($obj, 'attrD');
//        $this->assertTrue($obj->hasErrors('attrD'));
//    }

//    public function testErrorMessage()
//    {
//        $validator = new Boolean([
//            'trueValue' => true,
//            'falseValue' => false,
//            'strict' => true,
//        ]);
//        $validator->validate('someIncorrectValue', $errorMessage);
//
//        $this->assertEquals('the input value must be either "true" or "false".', $errorMessage);
//    }
}
