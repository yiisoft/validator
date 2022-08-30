<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

use InvalidArgumentException;
use Yiisoft\Validator\SkipOnEmptyCallback\SkipOnEmpty;

use function is_callable;

/**
 * @internal
 */
final class SkipOnEmptyNormalizer
{
    public static function normalize(mixed $skipOnEmpty): ?callable
    {
        if ($skipOnEmpty === false) {
            return null;
        }

        if ($skipOnEmpty === true) {
            return new SkipOnEmpty();
        }

        if (!is_callable($skipOnEmpty)) {
            throw new InvalidArgumentException('$skipOnEmpty must be a boolean or a callable.');
        }

        return $skipOnEmpty;
    }
}
