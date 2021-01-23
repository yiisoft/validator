<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

/**
 * Rule represents a single value validation rule.
 */
abstract class Rule implements RuleInterface, ParametrizedRuleInterface, FormatableRuleInterface
{
    private ?FormatterInterface $formatter = null;
    private bool $skipOnEmpty = false;
    private bool $skipOnError = true;

    /**
     * @var callable|null
     */
    private $when = null;

    /**
     * Validates the value
     *
     * @param mixed $value value to be validated
     * @param DataSetInterface|null $dataSet optional data set that could be used for contextual validation
     * @param bool $previousRulesErrored set to true if rule is part of a group of rules and one of the previous validations failed
     *
     * @return Result
     */
    final public function validate($value, DataSetInterface $dataSet = null, bool $previousRulesErrored = false): Result
    {
        if ($this->skipOnEmpty && $this->isEmpty($value)) {
            return new Result();
        }

        if (
          ($this->skipOnError && $previousRulesErrored) ||
          (is_callable($this->when) && !($this->when)($value, $dataSet))
        ) {
            return new Result();
        }

        return $this->validateValue($value, $dataSet);
    }

    /**
     * Validates the value. The method should be implemented by concrete validation rules.
     *
     * @param mixed $value value to be validated
     * @param DataSetInterface|null $dataSet optional data set that could be used for contextual validation
     *
     * @return Result
     */
    abstract protected function validateValue($value, DataSetInterface $dataSet = null): Result;

    public function formatter(FormatterInterface $formatter): self
    {
        $new = clone $this;
        $new->formatter = $formatter;
        return $new;
    }

    protected function formatMessage(string $message, array $parameters = []): string
    {
        if ($this->formatter === null) {
            $this->formatter = new Formatter();
        }

        return $this->formatter->format(
            $message,
            $parameters
        );
    }

    /**
     * Add a PHP callable whose return value determines whether this rule should be applied.
     * By default rule will be always applied.
     *
     * The signature of the callable should be `function ($value, DataSetInterface $dataSet): bool`, where $value and $dataSet
     * refer to the value validated and the data set in which context it is validated. The callable should return
     * a boolean value.
     *
     * The following example will enable the validator only when the country currently selected is USA:
     *
     * ```php
     * function ($value, DataSetInterface $dataSet) {
         return $dataSet->getAttributeValue('country') === Country::USA;
     }
     * ```
     *
     * @param callable $callback
     *
     * @return $this
     */
    public function when(callable $callback): self
    {
        $new = clone $this;
        $new->when = $callback;
        return $new;
    }

    public function skipOnError(bool $value): self
    {
        $new = clone $this;
        $new->skipOnError = $value;
        return $new;
    }

    /**
     * @param bool $value if validation should be skipped if value validated is empty
     *
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
     *
     * @param mixed $value the value to be checked
     *
     * @return bool whether the value is empty
     */
    protected function isEmpty($value): bool
    {
        return $value === null || $value === [] || $value === '';
    }

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
     *
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
