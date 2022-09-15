<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Yiisoft\Strings\NumericHelper;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Validator\Exception\UnexpectedRuleException;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\RuleHandlerInterface;
use Yiisoft\Validator\ValidationContext;

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
            $result->addError(
                message: $rule->isAsInteger() ? 'Value must be an integer.' : 'Value must be a number.',
                parameters: ['attribute' => $context->getAttribute(), 'value' => $value]
            );
            return $result;
        }

        $pattern = $rule->isAsInteger() ? $rule->getIntegerPattern() : $rule->getNumberPattern();

        if (!preg_match($pattern, NumericHelper::normalize($value))) {
            $result->addError(
                message: $rule->isAsInteger() ? 'Value must be an integer.' : 'Value must be a number.',
                parameters: ['attribute' => $context->getAttribute(), 'value' => $value]
            );
        } elseif ($rule->getMin() !== null && $value < $rule->getMin()) {
            $result->addError(
                message: $rule->getTooSmallMessage(),
                parameters: ['min' => $rule->getMin(), 'attribute' => $context->getAttribute(), 'value' => $value]
            );
        } elseif ($rule->getMax() !== null && $value > $rule->getMax()) {
            $result->addError(
                message: $rule->getTooBigMessage(),
                parameters: ['max' => $rule->getMax(), 'attribute' => $context->getAttribute(), 'value' => $value]
            );
        }

        return $result;
    }
}
