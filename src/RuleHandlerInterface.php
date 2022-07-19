<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

/**
 * Rule handler performs actual validation taking configuration parameters from a rule.
 */
interface RuleHandlerInterface
{
    /**
     * Validates the value.
     *
     * @param mixed $value Value to be validated.
     * @param object $rule Rule containing validation parameters.
     * @param ValidationContext|null $context Optional validation context.
     *
     * @internal Should be never called directly. Use {@see ValidatorInterface}.
     *
     * @return Result
     */
    public function validate(mixed $value, object $rule, ValidationContext $context): Result;
}
