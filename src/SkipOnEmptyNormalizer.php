<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

use InvalidArgumentException;
use Yiisoft\Validator\SkipOnEmptyCallback\SkipNone;
use Yiisoft\Validator\SkipOnEmptyCallback\SkipOnEmpty;

use function is_callable;

/**
 * @internal
 */
final class SkipOnEmptyNormalizer
{
    public static function normalize(mixed $skipOnEmpty): ?callable
    {
        if ($skipOnEmpty === false || $skipOnEmpty === null) {
            return new SkipNone();
        }

        if ($skipOnEmpty === true) {
            return new SkipOnEmpty();
        }

        if (is_callable($skipOnEmpty)) {
            return $skipOnEmpty;
        }

        throw new InvalidArgumentException('$skipOnEmpty must be a null, a boolean or a callable.');
    }
}
