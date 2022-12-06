<?php

declare(strict_types=1);

namespace Yiisoft\Validator\EmptyCriteria;

use function is_string;

/**
 * Empty criteria is a callable identifying when and which values exactly must be considered as empty for according
 * rules to be skipped or not skipped at all.
 *
 * With this criteria, a rule is skipped only when the validated value is empty: either not passed at all, `null`, an
 * empty string (not trimmed by default) or an empty array.
 *
 * Can be set:
 *
 * - At a rule level via `$skipOnEmpty` property, but only for rules implementing {@see SkipOnEmptyTrait} / including
 * {@see SkipOnEmptyTrait}.
 * - At validator level ({@see Validator::$defaultSkipOnEmptyCriteria}).
 *
 * A shortcut for `new NeverEmpty()` is `true` (string is not trimmed). If you want a string to be trimmed before
 * checking, use `new NeverEmpty(trimString: false)`.
 */
final class WhenEmpty
{
    public function __construct(
        /*
         * @var bool Whether to trim string (both from the start and from the end) before checking. Defaults to `false`
         * meaning no trimming is done.
         */
        private bool $trimString = false,
    )
    {
    }

    /**
     * @param mixed $value The validated value.
     * @param bool $isAttributeMissing A flag defining whether attribute is missing (not used / not passed at all).
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
