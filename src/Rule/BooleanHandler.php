<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Yiisoft\Validator\Exception\UnexpectedRuleException;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\RuleHandlerInterface;
use Yiisoft\Validator\ValidationContext;

/**
 * Checks if the value is a boolean value or a value corresponding to it.
 */
final class BooleanHandler implements RuleHandlerInterface
{
    public function validate(mixed $value, object $rule, ValidationContext $context): Result
    {
        if (!$rule instanceof Boolean) {
            throw new UnexpectedRuleException(Boolean::class, $rule);
        }

        if (!is_scalar($value)) {
            $valid = false;
        } elseif ($rule->isStrict()) {
            $valid = $value === $rule->getTrueValue() || $value === $rule->getFalseValue();
        } else {
            $valid = $value == $rule->getTrueValue() || $value == $rule->getFalseValue();
        }

        $result = new Result();
        if ($valid) {
            return $result;
        }

        $parameters = [
            'true' => $rule->getTrueValue() === true ? 'true' : $rule->getTrueValue(),
            'false' => $rule->getFalseValue() === false ? 'false' : $rule->getFalseValue(),
            'attribute' => $context->getAttribute(),
        ];
        is_scalar($value) ? $parameters['value'] = $value : $parameters['valueType'] = get_debug_type($value);

        $result->addError($rule->getMessage(), $parameters);

        return $result;
    }
}
