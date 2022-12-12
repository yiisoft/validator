<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Yiisoft\Strings\NumericHelper;
use Yiisoft\Validator\Exception\UnexpectedRuleException;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\RuleHandlerInterface;
use Yiisoft\Validator\ValidationContext;

use function is_bool;

/**
 * Validates that the value is a number.
 *
 * The format of the number must match the regular expression specified in {@see Number::$integerPattern}
 * or {@see Number::$numberPattern}. Optionally, you may configure the {@see Number::min()} and {@see Number::max()}
 * to ensure the number is within certain range.
 */
final class NumberHandler implements RuleHandlerInterface
{
    public function validate(mixed $value, object $rule, ValidationContext $context): Result
    {
        if (!$rule instanceof Number) {
            throw new UnexpectedRuleException(Number::class, $rule);
        }

        $result = new Result();

        if (is_bool($value) || !is_scalar($value)) {
            $result->addError($rule->getIncorrectInputMessage(), [
                'attribute' => $context->getTranslatedAttribute(),
                'type' => get_debug_type($value),
            ]);

            return $result;
        }

        $pattern = $rule->isAsInteger() ? $rule->getIntegerPattern() : $rule->getNumberPattern();

        if (!preg_match($pattern, NumericHelper::normalize($value))) {
            $result->addError($rule->getNotNumberMessage(), [
                'attribute' => $context->getTranslatedAttribute(),
                'value' => $value,
            ]);
        } elseif ($rule->getMin() !== null && $value < $rule->getMin()) {
            $result->addError($rule->getTooSmallMessage(), [
                'min' => $rule->getMin(),
                'attribute' => $context->getTranslatedAttribute(),
                'value' => $value,
            ]);
        } elseif ($rule->getMax() !== null && $value > $rule->getMax()) {
            $result->addError($rule->getTooBigMessage(), [
                'max' => $rule->getMax(),
                'attribute' => $context->getTranslatedAttribute(),
                'value' => $value,
            ]);
        }

        return $result;
    }
}
