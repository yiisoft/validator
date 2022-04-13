<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule\Regex;

use Yiisoft\Validator\Result;
use Yiisoft\Validator\ValidationContext;
use function is_string;

/**
 * Validates that the value matches the pattern specified in constructor.
 *
 * If the {@see Regex::$not} is used, the rule will ensure the value do NOT match the pattern.
 */
final class RegexValidator
{
    public static function getConfigClassName(): string
    {
        return Regex::class;
    }

    public function validate(mixed $value, object $config, ?ValidationContext $context = null): Result
    {
        $result = new Result();

        if (!is_string($value)) {
            $result->addError($config->incorrectInputMessage);

            return $result;
        }

        if (
            (!$config->not && !preg_match($config->pattern, $value)) ||
            ($config->not && preg_match($config->pattern, $value))
        ) {
            $result->addError($config->message);
        }

        return $result;
    }
}
