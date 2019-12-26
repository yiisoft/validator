<?php


namespace Yiisoft\Validator;

/**
 * DataSetInterface represents a key-value data set
 */
interface DataSetInterface
{
    public function getValue(string $attribute);
}
