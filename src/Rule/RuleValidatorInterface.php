<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Yiisoft\Validator\Result;
use Yiisoft\Validator\ValidationContext;
use Yiisoft\Validator\ValidatorInterface;

interface RuleValidatorInterface
{
    /**
     * Validates the value. The method should be implemented by concrete validation rules.
     *
     * @param mixed $value Value to be validated.
     * @param ValidationContext|null $context Optional validation context.
     *
     * @return Result
     */
    public function validate(mixed $value, object $rule, ValidatorInterface $validator, ?ValidationContext $context = null): Result;
}
