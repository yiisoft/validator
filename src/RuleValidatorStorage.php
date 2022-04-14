<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

use RuntimeException;
use Yiisoft\Validator\Rule\RuleValidatorInterface;

final class RuleValidatorStorage
{
    private array $validators;

    public function __construct(array $validators)
    {
        $this->validators = $validators;
    }

    public function getValidator(string $rule): RuleValidatorInterface
    {
        foreach ($this->validators as $ruleClassName => $validator) {
            if (is_a($rule, $ruleClassName, true)) {
                return $validator;
            }
        }

        throw new RuntimeException("No validator found for \"$rule\" rule.");
    }
}
