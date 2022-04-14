<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule\Regex;

use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\RuleValidatorInterface;
use Yiisoft\Validator\ValidationContext;
use Yiisoft\Validator\ValidatorInterface;
use function is_string;

/**
 * Validates that the value matches the pattern specified in constructor.
 *
 * If the {@see Regex::$not} is used, the rule will ensure the value do NOT match the pattern.
 */
final class RegexValidator implements RuleValidatorInterface
{
    public function validate(mixed $value, object $rule, ValidatorInterface $validator, ?ValidationContext $context = null): Result
    {
        $result = new Result();

        if (!is_string($value)) {
            $result->addError($rule->incorrectInputMessage);

            return $result;
        }

        if (
            (!$rule->not && !preg_match($rule->pattern, $value)) ||
            ($rule->not && preg_match($rule->pattern, $value))
        ) {
            $result->addError($rule->message);
        }

        return $result;
    }
}
