<?php

declare(strict_types=1);

namespace Yiisoft\Validator\EmptyCriteria;

use Yiisoft\Validator\Rule\Trait\SkipOnEmptyTrait;
use Yiisoft\Validator\Validator;

/**
 * Empty criteria is a callable identifying when and which values exactly must be considered as empty for according
 * rules to be skipped or not skipped at all.
 *
 * With this criteria a rule is never skipped (the validated value is always considered as not empty).
 *
 * Can be set:
 *
 * - At a rule level via `$skipOnEmpty` property, but only for rules implementing {@see SkipOnEmptyTrait} / including
 * {@see SkipOnEmptyTrait}.
 * - At validator level ({@see Validator::$defaultSkipOnEmptyCriteria}).
 *
 * A shortcut for `new NeverEmpty()` is `false`.
 */
final class NeverEmpty
{
    /**
     * @param mixed $value The validated value.
     * @param bool $isAttributeMissing A flag defining whether attribute is missing (not used / not passed at all).
     * @return bool Whether the validated value is considered empty.
     */
    public function __invoke(mixed $value, bool $isAttributeMissing): bool
    {
        return false;
    }
}
