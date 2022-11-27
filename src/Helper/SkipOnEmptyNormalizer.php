<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Helper;

use InvalidArgumentException;
use Yiisoft\Validator\EmptyCriteria\Never;
use Yiisoft\Validator\EmptyCriteria\WhenEmpty;

use function is_callable;

/**
 * @internal
 */
final class SkipOnEmptyNormalizer
{
    public static function normalize(mixed $skipOnEmpty): callable
    {
        if ($skipOnEmpty === false || $skipOnEmpty === null) {
            return new Never();
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
