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
     * @param mixed $value Value to be validated.
     * @param ValidationContext|null $context Optional validation context.
     *
     * @return Result
     */
    public function validate($value, ?ValidationContext $context = null): Result;
}
