<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule\Number;

use Yiisoft\Strings\NumericHelper;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\RuleValidatorInterface;
use Yiisoft\Validator\ValidationContext;
use Yiisoft\Validator\ValidatorInterface;

/**
 * Validates that the value is a number.
 *
 * The format of the number must match the regular expression specified in {@see Number::$integerPattern}
 * or {@see Number::$numberPattern}. Optionally, you may configure the {@see Number::min()} and {@see Number::max()}
 * to ensure the number is within certain range.
 */
final class NumberValidator implements RuleValidatorInterface
{
    public static function getRuleClassName(): string
    {
        return Number::class;
    }

    public function validate(mixed $value, object $config, ValidatorInterface $validator, ?ValidationContext $context = null): Result
    {
        $result = new Result();

        if (is_bool($value) || !is_scalar($value)) {
            $message = $config->asInteger ? 'Value must be an integer.' : 'Value must be a number.';
            $result->addError($message, ['value' => $value]);
            return $result;
        }

        $pattern = $config->asInteger ? $config->integerPattern : $config->numberPattern;

        if (!preg_match($pattern, NumericHelper::normalize($value))) {
            $message = $config->asInteger ? 'Value must be an integer.' : 'Value must be a number.';
            $result->addError($message, ['value' => $value]);
        } elseif ($config->min !== null && $value < $config->min) {
            $result->addError($config->tooSmallMessage, ['min' => $config->min]);
        } elseif ($config->max !== null && $value > $config->max) {
            $result->addError($config->tooBigMessage, ['max' => $config->max]);
        }

        return $result;
    }
}
