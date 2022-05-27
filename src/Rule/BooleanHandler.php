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

        if ($rule->isStrict()) {
            $valid = $value === $rule->getTrueValue() || $value === $rule->getFalseValue();
        } else {
            $valid = $value == $rule->getTrueValue() || $value == $rule->getFalseValue();
        }

        $result = new Result();

        if ($valid) {
            return $result;
        }

        $result->addError($rule->getMessage(), [
            // TODO: get reasons to do like this
            //  'true' => $config->getTrueValue() === true ? 'true' : $config->getTrueValue(),
            //  'false' => $config->$this->getFalseValue() === false ? 'false' : $config->$this->getFalseValue(),
            'true' => $rule->getTrueValue(),
            'false' => $rule->getFalseValue(),
        ]);

        return $result;
    }
}
