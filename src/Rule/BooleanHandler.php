<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Yiisoft\Validator\Exception\UnexpectedRuleException;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\RuleHandlerInterface;
use Yiisoft\Validator\ValidationContext;

/**
 * A handler for {@see Boolean} rule.
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
            'attribute' => $context->getTranslatedAttribute(),
            'true' => $rule->getTrueValue() === true ? 'true' : $rule->getTrueValue(),
            'false' => $rule->getFalseValue() === false ? 'false' : $rule->getFalseValue(),
        ];
        if ($value === null || is_scalar($value)) {
            $parameters['value'] = $value ?? 'null';

            return $result->addError($rule->getMessageWithValue(), $parameters);
        }

        $parameters['type'] = get_debug_type($value);

        return $result->addError($rule->getMessageWithType(), $parameters);
    }
}
