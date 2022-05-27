<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Yiisoft\Validator\Result;
use Yiisoft\Validator\ValidationContext;
use function is_string;
use Yiisoft\Validator\Exception\UnexpectedRuleException;

/**
 * Validates that the value matches the pattern specified in constructor.
 *
 * If the {@see Regex::$not} is used, the rule will ensure the value do NOT match the pattern.
 */
final class RegexHandler implements RuleHandlerInterface
{
    public function validate(mixed $value, object $rule, ?ValidationContext $context = null): Result
    {
        if (!$rule instanceof Regex) {
            throw new UnexpectedRuleException(Regex::class, $rule);
        }

        $result = new Result();

        if (!is_string($value)) {
            $result->addError($rule->getIncorrectInputMessage());

            return $result;
        }

        if (
            (!$rule->isNot() && !preg_match($rule->getPattern(), $value)) ||
            ($rule->isNot() && preg_match($rule->getPattern(), $value))
        ) {
            $result->addError($rule->getMessage());
        }

        return $result;
    }
}
