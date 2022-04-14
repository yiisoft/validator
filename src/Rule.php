<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

/**
 * Rule represents a single value validation rule.
 */
abstract class Rule
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
    final public function validate(mixed $value, ?ValidationContext $context = null): Result
    {
        return $this->validateValue($value, $context);
    }
}
