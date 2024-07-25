<?php

declare(strict_types=1);

namespace Yiisoft\Validator\EmptyCondition;

/**
 * Empty condition is a callable returning `true` if a value must be considered empty.
 *
 * With `WhenMissing` a rule is considered empty only when the value is missing. With regard to validation process, a
 * corresponding rule is skipped only if this condition is met and `WhenMissing` is set:
 *
 * - At a rule level via `$skipOnEmpty` property, but only for rules implementing {@see SkipOnEmptyTrait} / including
 * {@see SkipOnEmptyTrait}.
 * - At validator level ({@see Validator::$defaultSkipOnEmptyCondition}).
 *
 * There is no shortcut for this condition, because it's considered less used. Use new instance directly:
 * `new WhenMissing()`.
 */
final class WhenMissing
{
    /**
     * @param mixed $value The validated value.
     * @param bool $isPropertyMissing A flag defining whether the property is missing (not used / not passed at all).
     *
     * @return bool Whether the validated value is considered empty.
     */
    public function __invoke(mixed $value, bool $isPropertyMissing = false): bool
    {
        return $isPropertyMissing;
    }
}
