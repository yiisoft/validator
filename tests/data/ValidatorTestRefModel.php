<?php
namespace Yiisoft\Validator\Tests\data;

use Yiisoft\Validator\DataSetInterface;

class ValidatorTestRefModel implements DataSetInterface
{
    public $test_val = 2;
    public $test_val_fail = 99;

    public function getValue(string $key)
    {
        if (property_exists(self::class, $key)) {
            return $this->$key;
        }
    }

    public static function tableName()
    {
        return 'validator_ref';
    }

    public function getMain()
    {
        return $this->hasOne(ValidatorTestMainModel::class, ['id' => 'ref']);
    }
}
