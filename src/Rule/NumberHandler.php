<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Yiisoft\Strings\NumericHelper;
use Yiisoft\Validator\Exception\UnexpectedRuleException;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\Trait\LimitHandlerTrait;
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
    use LimitHandlerTrait;

    public function validate(mixed $value, object $rule, ValidationContext $context): Result
    {
        if (!$rule instanceof Number) {
            throw new UnexpectedRuleException(Number::class, $rule);
        }

        $result = new Result();

        if (is_bool($value) || !is_scalar($value)) {
            $result->addError($rule->getIncorrectInputMessage(), [
                'attribute' => $context->getAttribute(),
                'type' => get_debug_type($value),
            ]);

            return $result;
        }

        $pattern = $rule->isAsInteger() ? $rule->getIntegerPattern() : $rule->getNumberPattern();
        $value = NumericHelper::normalize($value);

        if (!preg_match($pattern, $value)) {
            return $result->addError($rule->getNotNumberMessage(), [
                'attribute' => $context->getAttribute(),
                'value' => $value,
            ]);
        }

        /**
         * @psalm-suppress InvalidOperand A value is guaranteed to be numeric here because of normalization via
         * `NumericHelper`and validation using regular expression  performed above.
         * @infection-ignore-all
         */
        $value = $value + 0;

        $this->validateLimits($rule, $context, $value, $result);

        return $result;
    }
}
