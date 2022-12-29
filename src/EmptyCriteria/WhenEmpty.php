<?php

declare(strict_types=1);

namespace Yiisoft\Validator\EmptyCriteria;

use function is_string;

/**
 * Empty criteria is a callable returning `true` if a value must be considered empty.
 *
 * With `WhenEmpty`, a value is considered empty only when it is either:
 *
 * - Not passed at all.
 * - `null`.
 * - An empty string (not trimmed by default).
 * - An empty array.
 *
 * With regard to validation process, a corresponding rule is skipped only if this condition is met and `WhenEmpty` is
 * set:
 *
 * - At a rule level via `$skipOnEmpty` property, but only for rules implementing {@see SkipOnEmptyTrait} / including
 * {@see SkipOnEmptyTrait}.
 * - At validator level ({@see Validator::$defaultSkipOnEmptyCriteria}).
 *
 * A shortcut for `new WhenEmpty()` is `true` (string is not trimmed). If you want a string to be trimmed before
 * checking, use `new WhenEmpty(trimString: false)`.
 */
final class WhenEmpty
{
    public function __construct(
        /*
         * @var bool Whether to trim string (both from the start and from the end) before checking. Defaults to `false`
         * meaning no trimming is done.
         */
        private bool $trimString = false,
    ) {
    }

    /**
     * @param mixed $value The validated value.
     * @param bool $isAttributeMissing A flag defining whether the attribute is missing (not used / not passed at all).
     *
     * @return bool Whether the validated value is considered empty.
     */
    public function __invoke(mixed $value, bool $isAttributeMissing): bool
    {
        if (is_string($value) && $this->trimString) {
            $value = trim($value);
        }

        return $isAttributeMissing || $value === null || $value === [] || $value === '';
    }
}
