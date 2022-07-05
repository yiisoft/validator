<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Yiisoft\Validator\Result;
use Yiisoft\Validator\RuleInterface;
use Yiisoft\Validator\ValidationContext;

/**
 * Rule handler performs actual validation taking configuration parameters from a rule.
 */
interface SelfValidatableRuleInterface extends RuleInterface
{
    /**
     * Validates the value.
     *
     * @param mixed $value Value to be validated.
     * @param ValidationContext|null $context Optional validation context.
     *
     * @return Result
     */
    public function validate(mixed $value, ?ValidationContext $context = null): Result;
}
