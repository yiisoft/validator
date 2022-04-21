<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Yiisoft\Strings\NumericHelper;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\ValidationContext;
use Yiisoft\Validator\ValidatorInterface;
use Yiisoft\Validator\Exception\UnexpectedRuleException;

/**
 * Validates that the value is a number.
 *
 * The format of the number must match the regular expression specified in {@see Number::$integerPattern}
 * or {@see Number::$numberPattern}. Optionally, you may configure the {@see Number::min()} and {@see Number::max()}
 * to ensure the number is within certain range.
 */
final class NumberHandler implements RuleHandlerInterface
{
    public function validate(mixed $value, object $rule, ValidatorInterface $validator, ?ValidationContext $context = null): Result
    {
        if (!$rule instanceof Number) {
            throw new UnexpectedRuleException(Number::class, $rule);
        }

        $result = new Result();

        if (is_bool($value) || !is_scalar($value)) {
            $message = $rule->asInteger ? 'Value must be an integer.' : 'Value must be a number.';
            $result->addError($message, ['value' => $value]);
            return $result;
        }

        $pattern = $rule->asInteger ? $rule->integerPattern : $rule->numberPattern;

        if (!preg_match($pattern, NumericHelper::normalize($value))) {
            $message = $rule->asInteger ? 'Value must be an integer.' : 'Value must be a number.';
            $result->addError($message, ['value' => $value]);
        } elseif ($rule->min !== null && $value < $rule->min) {
            $result->addError($rule->tooSmallMessage, ['min' => $rule->min]);
        } elseif ($rule->max !== null && $value > $rule->max) {
            $result->addError($rule->tooBigMessage, ['max' => $rule->max]);
        }

        return $result;
    }
}
