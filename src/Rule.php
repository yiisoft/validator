<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

/**
 * Rule represents a single value validation rule.
 */
abstract class Rule
{
    private bool $skipOnEmpty = false;

    /**
     * Validates the value
     *
     * @param mixed $value value to be validated
     * @param DataSetInterface|null $dataSet optional data set that could be used for contextual validation
     * @return Result
     */
    final public function validate($value, DataSetInterface $dataSet = null): Result
    {
        if ($this->skipOnEmpty && $this->isEmpty($value)) {
            return new Result();
        }

        return $this->validateValue($value, $dataSet);
    }

    /**
     * Validates the value. The method should be implemented by concrete validation rules.
     *
     * @param mixed $value value to be validated
     * @param DataSetInterface|null $dataSet optional data set that could be used for contextual validation
     * @return Result
     */
    abstract protected function validateValue($value, DataSetInterface $dataSet = null): Result;

    /**
     * @param bool $value if validation should be skipped if value validated is empty
     * @return self
     */
    public function skipOnEmpty(bool $value): self
    {
        $new = clone $this;
        $new->skipOnEmpty = $value;
        return $new;
    }

    /**
     * Checks if the given value is empty.
     * A value is considered empty if it is null, an empty array, or an empty string.
     * Note that this method is different from PHP empty(). It will return false when the value is 0.
     * @param mixed $value the value to be checked
     * @return bool whether the value is empty
     */
    protected function isEmpty($value): bool
    {
        return $value === null || $value === [] || $value === '';
    }
}
