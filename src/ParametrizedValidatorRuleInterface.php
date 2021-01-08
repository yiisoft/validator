<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

/**
 * Rule represents a single value validation rule.
 */
interface ParametrizedValidatorRuleInterface extends ValidatorRuleInterface
{
    /**
     * Get name of the rule to be used when rule is converted to array.
     * By default it returns base name of the class, first letter in lowercase.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Returns rule options as array.
     *
     * @return array
     */
    public function getOptions(): array;
}
