<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

/**
 * Rule represents a single value validation rule.
 */
interface RuleInterface
{
    /**
     * Validates the value
     *
     * @param mixed $value value to be validated
     * @param DataSetInterface|null $dataSet optional data set that could be used for contextual validation
     * @param bool $previousRulesErrored set to true if rule is part of a group of rules and one of the previous validations failed
     *
     * @return Result
     */
    public function validate($value, DataSetInterface $dataSet = null, bool $previousRulesErrored = false): Result;
}
