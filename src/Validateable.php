<?php

namespace Yiisoft\Validator;

interface Validateable
{
    /**
     * @param mixed $value
     * @param DataSetInterface|null $dataSet
     * @param bool $previousRulesErrored
     * @return Result
     */
    public function validate($value, DataSetInterface $dataSet = null, bool $previousRulesErrored = false): Result;
}
