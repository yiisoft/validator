<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Helper;

use InvalidArgumentException;
use Yiisoft\Validator\EmptyCondition\NeverEmpty;
use Yiisoft\Validator\EmptyCondition\WhenEmpty;
use Yiisoft\Validator\SkipOnEmptyInterface;

use function is_callable;

/**
 * A helper class used to normalize different types of "skip on empty" values including shortcuts to a callable
 * ({@see SkipOnEmptyInterface}).
 *
 * @internal
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
     * @param mixed $skipOnEmpty Raw "skip on empty" value of any type.
     *
     * @throws InvalidArgumentException If the type of {@see $skipOnEmpty} is not valid.
     *
     * @return callable An empty condition as a callable.
     */
    public static function normalize(mixed $skipOnEmpty): callable
    {
        if ($skipOnEmpty === false || $skipOnEmpty === null) {
            return new NeverEmpty();
        }

        if ($skipOnEmpty === true) {
            return new WhenEmpty();
        }

        if (is_callable($skipOnEmpty)) {
            return $skipOnEmpty;
        }

        throw new InvalidArgumentException('$skipOnEmpty must be a null, a boolean or a callable.');
    }
}
