<?php
namespace Yiisoft\Validator\Tests\data;

use Yiisoft\Validator\DataSetInterface;

class FakedValidationModel implements DataSetInterface
{

    public $attr_timestamp = false;

    public $attr_date;

    public function getValue(string $key)
    {
        // TODO: Implement getValue() method.
    }

    public static function createWithAttributes($attributes): FakedValidationModel
    {
        $model = new self();
        foreach ($attributes as $attribute => $value) {
            $model->$attribute = $value;
        }

        return $model;
    }
}
