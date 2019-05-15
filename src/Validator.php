<?php


namespace Yiisoft\Validator;

class Validator
{
    /**
     * @param mixed $value
     * @param Rule[] $rules
     * @return Result
     */
    public function validateValue($value, $rules): Result
    {
        foreach ($rules as $rule)
        {

        }
    }

    public function validateData(DataSet $dataSet, $rules): ResultSet
    {

    }

    public function validate($object): ResultSet
    {
        if (!$object instanceof DataSet) {
            throw new \InvalidArgumentException('bla bla');
        }

    }
}
