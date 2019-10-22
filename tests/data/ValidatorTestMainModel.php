<?php
namespace Yiisoft\Validator\Tests\data;


use Yiisoft\Validator\DataSetInterface;

class ValidatorTestMainModel implements DataSetInterface
{
    public $testMainVal = 1;

    public function getValue(string $key)
    {
        if (property_exists(self::class, $key)) {
            return $this->$key;
        }
    }

    public static function tableName()
    {
        return 'validator_main';
    }

    public function getReferences()
    {
        return $this->hasMany(ValidatorTestRefModel::class, ['ref' => 'id']);
    }
}
