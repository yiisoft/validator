<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

use function is_callable;

/**
 * Rule represents a single value validation rule.
 */
abstract class Rule implements ParametrizedRuleInterface
{
    public function __construct(
        private ?FormatterInterface $formatter = null,
        /**
         * @var bool if validation should be skipped if value validated is empty
         */
        private bool $skipOnEmpty = false,
        private bool $skipOnError = false,
        /**
         * @var callable|null
         *
         * Add a PHP callable whose return value determines whether this rule should be applied.
         * By default, rule will always be applied.
         *
         * The signature of the callable should be `function ($value, ValidationContext $context): bool`,
         * where `$value` and `$context` refer to the value validated and the validation context.
         * The callable should return a boolean value.
         *
         * The following example will enable the validator only when the country currently selected is USA:
         *
         * ```php
         * function ($value, ValidationContext $context)) {
         *     if ($context === null) {
         *         return false;
         *     }
         *
         *     $dataSet = $context->getDataSet();
         *     if ($dataSet === null) {
         *         return false;
         *     }
         *
         *     return $dataSet->getAttributeValue('country') === Country::USA;
         * }
         * ```
         */
        private $when = null,
    ) {
    }

    /**
     * Validates the value
     *
     * @param mixed $value Value to be validated.
     * @param ValidationContext|null $context Optional validation context.
     *
     * @return Result
     */
    final public function validate($value, ?ValidationContext $context = null): Result
    {
        if ($this->skipOnEmpty && $this->isEmpty($value)) {
            return new Result();
        }

        if ($this->skipOnError && $context?->getParameter(RuleSet::PARAMETER_PREVIOUS_RULES_ERRORED) === true) {
            return new Result();
        }

        if (is_callable($this->when) && !($this->when)($value, $context)) {
            return new Result();
        }

        return $this->validateValue($value, $context);
    }

    /**
     * Validates the value. The method should be implemented by concrete validation rules.
     *
     * @param mixed $value Value to be validated.
     * @param ValidationContext|null $context Optional validation context.
     *
     * @return Result
     */
    abstract protected function validateValue($value, ?ValidationContext $context = null): Result;

    protected function formatMessage(string $message, array $parameters = []): string
    {
        if ($this->formatter === null) {
            $this->formatter = new Formatter();
        }

        return $this->formatter->format($message, $parameters);
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
     * By default, it returns base name of the class, first letter in lowercase.
     */
    public function getName(): string
    {
        $className = static::class;
        return lcfirst(substr($className, strrpos($className, '\\') + 1));
    }

    /**
     * Returns rule options as array.
     */
    public function getOptions(): array
    {
        return [
            'skipOnEmpty' => $this->skipOnEmpty,
            'skipOnError' => $this->skipOnError,
        ];
    }
}
