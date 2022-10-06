<?php

declare(strict_types=1);

namespace Yiisoft\Validator\SkipOnEmptyCallback;

final class SkipOnEmpty
{
    public function __invoke(mixed $value, bool $isAttributeMissing): bool
    {
        return $isAttributeMissing || $value === null || $value === [] || $value === '';
    }
}
