<?php

namespace Yiisoft\Validator;

interface RuleInterface
{
    /**
     * @param mixed $value
     * @param DataSetInterface|null $dataSet
     * @param bool $previousRulesErrored
     * @return Result
     */
    public function validate($value, DataSetInterface $dataSet = null, bool $previousRulesErrored = false): Result;

    public function skipOnError(bool $value);

    public function skipOnEmpty(bool $value);
}
