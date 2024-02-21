<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Helper;

use Yiisoft\Validator\EmptyCondition\NeverEmpty;
use Yiisoft\Validator\EmptyCondition\WhenEmpty;
use Yiisoft\Validator\SkipOnEmptyInterface;

/**
 * A helper class used to normalize different types of "skip on empty" values including shortcuts to a callable
 * ({@see SkipOnEmptyInterface}).
 *
 * @internal
 *
 * @psalm-import-type SkipOnEmptyCallable from SkipOnEmptyInterface
 * @psalm-import-type SkipOnEmptyValue from SkipOnEmptyInterface
 */
final class SkipOnEmptyNormalizer
{
    /**
     * Normalizes different types of "skip on empty" values including shortcuts to a callable:
     *
     * - `null` and `false` values are normalized to {@see NeverEmpty}.
     * - `true` value is normalized to {@see WhenEmpty}.
     * - A callable is left as is.
     * - Other types are rejected causing the exception.
     *
     * @param bool|callable|null $skipOnEmpty Raw "skip on empty" value of any type.
     *
     * @return callable An empty condition as a callable.
     *
     * @psalm-param SkipOnEmptyValue $skipOnEmpty
     * @psalm-return SkipOnEmptyCallable
     */
    public static function normalize(bool|callable|null $skipOnEmpty): callable
    {
        if ($skipOnEmpty === false || $skipOnEmpty === null) {
            return new NeverEmpty();
        }

        if ($skipOnEmpty === true) {
            return new WhenEmpty();
        }

        return $skipOnEmpty;
    }
}
