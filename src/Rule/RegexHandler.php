<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Yiisoft\Validator\Exception\UnexpectedRuleException;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\RuleHandlerInterface;
use Yiisoft\Validator\RuleInterface;
use Yiisoft\Validator\ValidationContext;

use function is_string;

/**
 * Validates that the value matches the pattern specified in constructor.
 *
 * @see Regex
 */
final class RegexHandler implements RuleHandlerInterface
{
    public function validate(mixed $value, RuleInterface $rule, ValidationContext $context): Result
    {
        if (!$rule instanceof Regex) {
            throw new UnexpectedRuleException(Regex::class, $rule);
        }

        $result = new Result();
        if (!is_string($value)) {
            return $result->addError($rule->getIncorrectInputMessage(), [
                'property' => $context->getTranslatedProperty(),
                'Property' => $context->getCapitalizedTranslatedProperty(),
                'type' => get_debug_type($value),
            ]);
        }

        if (
            (!$rule->isNot() && !preg_match($rule->getPattern(), $value)) ||
            ($rule->isNot() && preg_match($rule->getPattern(), $value))
        ) {
            $result->addError($rule->getMessage(), [
                'property' => $context->getTranslatedProperty(),
                'Property' => $context->getCapitalizedTranslatedProperty(),
                'value' => $value,
            ]);
        }

        return $result;
    }
}
