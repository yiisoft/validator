<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

/**
 * Rule represents a single value validation rule.
 */
abstract class Rule implements RuleInterface
{
    use SkippableTrait, ValueTrait;

    /**
     * Validates the value
     *
     * @param mixed $value value to be validated
     * @param DataSetInterface|null $dataSet optional data set that could be used for contextual validation
     * @param bool $previousRulesErrored set to true if rule is part of a group of rules and one of the previous validations failed
     * @return Error
     */
    final public function validate($value, DataSetInterface $dataSet = null, bool $previousRulesErrored = false): Error
    {
        if ($this->skipOnEmpty && $this->isEmpty($value)) {
            return new Error();
        }

        if ($this->skipOnError && $previousRulesErrored) {
            return new Error();
        }

        return $this->validateValue($value, $dataSet);
    }

    /**
     * Validates the value. The method should be implemented by concrete validation rules.
     *
     * @param mixed $value value to be validated
     * @param DataSetInterface|null $dataSet optional data set that could be used for contextual validation
     * @return Error
     */
    abstract protected function validateValue($value, DataSetInterface $dataSet = null): Error;

    /**
     * Get name of the rule to be used when rule is converted to array.
     * By default it returns base name of the class, first letter in lowercase.
     *
     * @return string
     */
    public function getName(): string
    {
        $className = static::class;
        return lcfirst(substr($className, strrpos($className, '\\') + 1));
    }

    /**
     * Returns rule options as array.
     * @return array
     */
    public function getOptions(): array
    {
        return [
            'skipOnEmpty' => $this->skipOnEmpty,
            'skipOnError' => $this->skipOnError,
        ];
    }
}
