<?php


namespace Yiisoft\Validator;


interface DataSet
{
    public function getValue(string $key): array;
}
