<?php

declare(strict_types=1);

namespace Yiisoft\Validator\EmptyCondition;

use Yiisoft\Validator\Rule\Trait\SkipOnEmptyTrait;
use Yiisoft\Validator\Validator;

/**
 * Empty condition is a callable returning `true` if a value must be considered empty.
 *
 * With `NeverEmpty` a value is always considered non-empty. With regard to validation process, a corresponding rule is
 * never skipped if `NeverEmpty` is set:
 *
 * - At a rule level via `$skipOnEmpty` property, but only for rules implementing {@see SkipOnEmptyTrait} / including
 * {@see SkipOnEmptyTrait}.
 * - At validator level ({@see Validator::$defaultSkipOnEmptyCondition}).
 *
 * This is a default behavior for all built-in rules.
 *
 * A shortcut for `new NeverEmpty()` is `false`.
 */
final class NeverEmpty
{
    /**
     * @param bool $isAttributeMissing A flag defining whether the attribute is missing (not used / not passed at all).
     * @return bool Whether the validated value is considered empty.
     */
    public function __invoke(mixed $value, bool $isAttributeMissing = false): bool
    {
        return false;
    }
}
