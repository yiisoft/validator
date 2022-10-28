<?php

declare(strict_types=1);

namespace Yiisoft\Validator\SkipOnEmptyCallback;

use function is_string;

final class SkipOnEmpty
{
    public function __construct(private bool $trimString = false)
    {
    }

    public function __invoke(mixed $value, bool $isAttributeMissing): bool
    {
        if (is_string($value) && $this->trimString) {
            $value = trim($value);
        }

        return $isAttributeMissing || $value === null || $value === [] || $value === '';
    }
}
