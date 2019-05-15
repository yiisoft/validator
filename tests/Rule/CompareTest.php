<?php

namespace Yiisoft\Validator\Tests\Rule;

use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\DataSet;
use Yiisoft\Validator\Rule\Compare;

/**
 * @group validators
 */
class CompareTest extends TestCase
{
    public function testValidateValueException()
    {
        $this->expectException(\RuntimeException::class);
        $val = new Compare();
        $val->validateValue('val');
    }

    public function testValidateValue()
    {
        $value = 18449;
        // default config
        $val = (new Compare())->withValue($value);
        $this->assertTrue($val->validateValue($value)->isValid());
        $this->assertTrue($val->validateValue((string)$value)->isValid());
        $this->assertFalse($val->validateValue($value + 1)->isValid());
        foreach ($this->getOperationTestData($value) as $operator => $tests) {
            $val = (new Compare())->withValue($value);
            $val->operator($operator);
            foreach ($tests as $test) {
                $this->assertEquals($test[1], $val->validateValue($test[0])->isValid(), "Testing $operator");
            }
        }
    }

    protected function getOperationTestData($value)
    {
        return [
            '===' => [
                [$value, true],
                [(string)$value, true],
                [(float)$value, true],
                [$value + 1, false],
            ],
            '!=' => [
                [$value, false],
                [(string)$value, false],
                [(float)$value, false],
                [$value + 0.00001, true],
                [false, true],
            ],
            '!==' => [
                [$value, false],
                [(string)$value, false],
                [(float)$value, false],
                [false, true],
            ],
            '>' => [
                [$value, false],
                [$value + 1, true],
                [$value - 1, false],
            ],
            '>=' => [
                [$value, true],
                [$value + 1, true],
                [$value - 1, false],
            ],
            '<' => [
                [$value, false],
                [$value + 1, false],
                [$value - 1, true],
            ],
            '<=' => [
                [$value, true],
                [$value + 1, false],
                [$value - 1, true],
            ],
            /*'non-op' => [
                [$value, false],
                [$value + 1, false],
                [$value - 1, false],
            ],*/
        ];
    }

    public function testValidateAttribute()
    {
        // invalid-array
        $val = new Compare();
        $model = new FakedValidationModel();
        $model->attr = ['test_val'];
        $val->validateAttribute($model, 'attr');
        $this->assertTrue($model->hasErrors('attr'));
        $val = new Compare(['compareValue' => 'test-string']);
        $model = new FakedValidationModel();
        $model->attr_test = 'test-string';
        $val->validateAttribute($model, 'attr_test');
        $this->assertFalse($model->hasErrors('attr_test'));
        $val = new Compare(['compareAttribute' => 'attr_test_val']);
        $model = new FakedValidationModel();
        $model->attr_test = 'test-string';
        $model->attr_test_val = 'test-string';
        $val->validateAttribute($model, 'attr_test');
        $this->assertFalse($model->hasErrors('attr_test'));
        $this->assertFalse($model->hasErrors('attr_test_val'));
        $val = new Compare(['compareAttribute' => 'attr_test_val']);
        $model = new FakedValidationModel();
        $model->attr_test = 'test-string';
        $model->attr_test_val = 'test-string-false';
        $val->validateAttribute($model, 'attr_test');
        $this->assertTrue($model->hasErrors('attr_test'));
        $this->assertFalse($model->hasErrors('attr_test_val'));
        // assume: _repeat
        $val = new Compare();
        $model = new FakedValidationModel();
        $model->attr_test = 'test-string';
        $model->attr_test_repeat = 'test-string';
        $val->validateAttribute($model, 'attr_test');
        $this->assertFalse($model->hasErrors('attr_test'));
        $this->assertFalse($model->hasErrors('attr_test_repeat'));
        $val = new Compare();
        $model = new FakedValidationModel();
        $model->attr_test = 'test-string';
        $model->attr_test_repeat = 'test-string2';
        $val->validateAttribute($model, 'attr_test');
        $this->assertTrue($model->hasErrors('attr_test'));
        $this->assertFalse($model->hasErrors('attr_test_repeat'));
        // not existing op
        $val = new Compare();
        $val->operator = '<>';
        $model = FakedValidationModel::createWithAttributes(['attr_o' => 5, 'attr_o_repeat' => 5]);
        $val->validateAttribute($model, 'attr_o');
        $this->assertTrue($model->hasErrors('attr_o'));
    }

    public function testAttributeErrorMessages()
    {
        $model = new class implements DataSet
        {
            public function getValue(string $key)
            {
                $data = [
                    'attr1' => 1,
                    'attr2' => 2,
                    'attrN' => 2,
                ];

                if (isset($data[$key])) {
                    return $data[$key];
                }

                throw new \RuntimeException("There is no property $key.");
            }
        };

        foreach ($this->getTestDataForMessages() as $data) {
            $validator = new Compare();
            $validator->operator($data[1]);
            $validator->message(null); // TODO: what for?
            // $validator->init(); // reload messages
            $validator->{$data[4]} = $data[2];
            $result = $validator->validateAttribute($model, $data[0]);
            $error = $result->getErrors()[0];
            $this->assertEquals($data[3], $error);
        }
    }

    protected function getTestDataForMessages()
    {
        return [
            ['attr1', '==', 2, 'attr1 must be equal to "2".', 'compareValue'],
            ['attr1', '===', 2, 'attr1 must be equal to "2".', 'compareValue'],
            ['attrN', '!=', 2, 'attrN must not be equal to "2".', 'compareValue'],
            ['attrN', '!==', 2, 'attrN must not be equal to "2".', 'compareValue'],
            ['attr1', '>', 2, 'attr1 must be greater than "2".', 'compareValue'],
            ['attr1', '>=', 2, 'attr1 must be greater than or equal to "2".', 'compareValue'],
            ['attr2', '<', 1, 'attr2 must be less than "1".', 'compareValue'],
            ['attr2', '<=', 1, 'attr2 must be less than or equal to "1".', 'compareValue'],

            ['attr1', '==', 'attr2', 'attr1 must be equal to "attr2".', 'compareAttribute'],
            ['attr1', '===', 'attr2', 'attr1 must be equal to "attr2".', 'compareAttribute'],
            ['attrN', '!=', 'attr2', 'attrN must not be equal to "attr2".', 'compareAttribute'],
            ['attrN', '!==', 'attr2', 'attrN must not be equal to "attr2".', 'compareAttribute'],
            ['attr1', '>', 'attr2', 'attr1 must be greater than "attr2".', 'compareAttribute'],
            ['attr1', '>=', 'attr2', 'attr1 must be greater than or equal to "attr2".', 'compareAttribute'],
            ['attr2', '<', 'attr1', 'attr2 must be less than "attr1".', 'compareAttribute'],
            ['attr2', '<=', 'attr1', 'attr2 must be less than or equal to "attr1".', 'compareAttribute'],
        ];
    }

    public function testValidateAttributeOperators()
    {
        $value = 55;
        foreach ($this->getOperationTestData($value) as $operator => $tests) {
            $val = (new Compare())->operator($operator)->withValue($value);
            foreach ($tests as $test) {
                $model = new FakedValidationModel();
                $model->attr_test = $test[0];
                $val->validateAttribute($model, 'attr_test');
                $this->assertEquals($test[1], !$model->hasErrors('attr_test'));
            }
        }
    }

    public function testEnsureMessageSetOnInit()
    {
        foreach ($this->getOperationTestData(1337) as $operator => $tests) {
            $val = (new Compare())->operator($operator);
            $this->assertTrue(strlen($val->message) > 1);
        }
        try {
            (new Compare())->operator('<>');
        } catch (\RuntimeException $e) {
            return;
        } catch (\Exception $e) {
            $this->fail('InvalidConfigException expected' . get_class($e) . 'received');

            return;
        }
        $this->fail('InvalidConfigException expected none received');
    }
}
