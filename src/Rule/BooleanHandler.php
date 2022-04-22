<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Yiisoft\Validator\Result;
use Yiisoft\Validator\ValidationContext;
use Yiisoft\Validator\Exception\UnexpectedRuleException;

/**
 * Checks if the value is a boolean value or a value corresponding to it.
 */
final class BooleanHandler implements RuleHandlerInterface
{
    public function validate(mixed $value, object $rule, ?ValidationContext $context = null): Result
    {
        if (!$rule instanceof Boolean) {
            throw new UnexpectedRuleException(Boolean::class, $rule);
        }

        if ($rule->strict) {
            $valid = $value === $rule->trueValue || $value === $rule->falseValue;
        } else {
            $valid = $value == $rule->trueValue || $value == $rule->falseValue;
        }

        $result = new Result();

        if ($valid) {
            return $result;
        }

        $result->addError($rule->message, [
            // TODO: get reasons to do like this
            //            'true' => $config->trueValue === true ? 'true' : $config->trueValue,
            //            'false' => $config->falseValue === false ? 'false' : $config->falseValue,
            'true' => $rule->trueValue,
            'false' => $rule->falseValue,
        ]);

        return $result;
    }
}
