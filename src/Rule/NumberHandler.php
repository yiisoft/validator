<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Yiisoft\Strings\NumericHelper;
use Yiisoft\Strings\StringHelper;
use Yiisoft\Validator\Exception\UnexpectedRuleException;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\RuleHandlerInterface;
use Yiisoft\Validator\ValidationContext;

use function is_bool;

/**
 * Validates that the value is a number.
 *
 * @see Number
 */
final class NumberHandler implements RuleHandlerInterface
{
    public function validate(mixed $value, object $rule, ValidationContext $context): Result
    {
        if (!$rule instanceof AbstractNumber) {
            throw new UnexpectedRuleException(AbstractNumber::class, $rule);
        }

        $result = new Result();

        if (is_bool($value) || !is_scalar($value)) {
            $result->addError($rule->getIncorrectInputMessage(), [
                'attribute' => $context->getTranslatedAttribute(),
                'Attribute' => $context->getCapitalizedTranslatedAttribute(),
                'type' => get_debug_type($value),
            ]);

            return $result;
        }

        if (!preg_match($rule->getPattern(), NumericHelper::normalize($value))) {
            $result->addError($rule->getNotNumberMessage(), [
                'attribute' => $context->getTranslatedAttribute(),
                'Attribute' => $context->getCapitalizedTranslatedAttribute(),
                'value' => $value,
            ]);
        } elseif ($rule->getMin() !== null && $value < $rule->getMin()) {
            $result->addError($rule->getLessThanMinMessage(), [
                'min' => $rule->getMin(),
                'attribute' => $context->getTranslatedAttribute(),
                'Attribute' => $context->getCapitalizedTranslatedAttribute(),
                'value' => $value,
            ]);
        } elseif ($rule->getMax() !== null && $value > $rule->getMax()) {
            $result->addError($rule->getGreaterThanMaxMessage(), [
                'max' => $rule->getMax(),
                'attribute' => $context->getTranslatedAttribute(),
                'Attribute' => $context->getCapitalizedTranslatedAttribute(),
                'value' => $value,
            ]);
        }

        return $result;
    }
}
