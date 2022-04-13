<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule\Boolean;

use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\RuleValidatorInterface;
use Yiisoft\Validator\ValidationContext;

/**
 * Checks if the value is a boolean value or a value corresponding to it.
 */
final class BooleanValidator implements RuleValidatorInterface
{
    public static function getConfigClassName(): string
    {
        return Boolean::class;
    }

    public function validate(mixed $value, object $config, ?ValidationContext $context = null): Result
    {
        if ($config->strict) {
            $valid = $value === $config->trueValue || $value === $config->falseValue;
        } else {
            $valid = $value == $config->trueValue || $value == $config->falseValue;
        }

        $result = new Result();

        if ($valid) {
            return $result;
        }

        $result->addError($config->message, [
            // TODO: get reasons to do like this
//            'true' => $config->trueValue === true ? 'true' : $config->trueValue,
//            'false' => $config->falseValue === false ? 'false' : $config->falseValue,
            'true' => $config->trueValue,
            'false' => $config->falseValue,
        ]);

        return $result;
    }
}
