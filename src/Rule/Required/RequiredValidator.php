<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule\Required;

use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\RuleValidatorInterface;
use Yiisoft\Validator\ValidationContext;
use function is_string;

/**
 * Validates that the specified value is neither null nor empty.
 */
final class RequiredValidator implements RuleValidatorInterface
{
    public static function getConfigClassName(): string
    {
        return Required::class;
    }

    public function validate(mixed $value, object $config, ?ValidationContext $context = null): Result
    {
        $result = new Result();

        if ($this->isEmpty(is_string($value) ? trim($value) : $value)) {
            $result->addError($config->message);
        }

        return $result;
    }

    /**
     * Checks if the given value is empty.
     * A value is considered empty if it is null, an empty array, or an empty string.
     * Note that this method is different from PHP empty(). It will return false when the value is 0.
     *
     * @param mixed $value the value to be checked
     *
     * @return bool whether the value is empty
     */
    private function isEmpty(mixed $value): bool
    {
        return $value === null || $value === [] || $value === '';
    }
}
