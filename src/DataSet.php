<?php


namespace Yii\Validator;


interface DataSet
{
    public function getValue(string $key): array;
}
