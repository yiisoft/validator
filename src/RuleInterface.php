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

    /**
     * @param bool $value if validation should be skipped if value validated is empty
     * @return self
     */
    public function skipOnError(bool $value);

    public function skipOnEmpty(bool $value);

    /**
     * Get name of the rule to be used when rule is converted to array.
     * By default it returns base name of the class, first letter in lowercase.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Returns rule options as array.
     * @return array
     */
    public function getOptions(): array;
}
