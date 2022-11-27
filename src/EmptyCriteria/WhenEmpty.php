<?php

declare(strict_types=1);

namespace Yiisoft\Validator\EmptyCriteria;

use function is_string;

final class WhenEmpty
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
