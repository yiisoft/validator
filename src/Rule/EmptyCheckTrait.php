<?php
declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

trait EmptyCheckTrait
{
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
