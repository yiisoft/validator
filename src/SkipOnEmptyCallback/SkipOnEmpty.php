<?php

declare(strict_types=1);

namespace Yiisoft\Validator\SkipOnEmptyCallback;

use function is_string;

final class SkipOnEmpty
{
    public function __invoke(mixed $value, bool $isAttributeMissing, bool $trimString = false): bool
    {
        if (is_string($value)) {
            $value = trim($value);

            return $value === '';
        }

        return $isAttributeMissing || $value === null || $value === [];
    }
}
