<?php

declare(strict_types=1);

namespace Yiisoft\Validator\EmptyCriteria;

/**
 * Empty criteria is a callable returning `true` if a value must be considered empty.
 *
 * With `WhenNull` a rule is considered empty only when the value is `null`. With regard to validation process, a
 * corresponding rule is skipped only if this condition is met and `WhenNull` is set:
 *
 * - At a rule level via `$skipOnEmpty` property, but only for rules implementing {@see SkipOnEmptyTrait} / including
 * {@see SkipOnEmptyTrait}.
 * - At validator level ({@see Validator::$defaultSkipOnEmptyCriteria}).
 *
 * There is no shortcut for this criteria, because it's considered less used. Use new instance directly:
 * `new WhenNull()`.
 */
final class WhenNull
{
    /**
     * @param mixed $value The validated value.
     * @param bool $isAttributeMissing A flag defining whether the attribute is missing (not used / not passed at all).
     *
     * @return bool Whether the validated value is considered empty.
     */
    public function __invoke(mixed $value, bool $isAttributeMissing): bool
    {
        return $value === null;
    }
}
