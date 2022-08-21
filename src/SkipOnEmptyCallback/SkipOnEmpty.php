<?php

declare(strict_types=1);

namespace Yiisoft\Validator\SkipOnEmptyCallback;

final class SkipOnEmpty
{
    public function __invoke(mixed $value): bool
    {
        return $value === null || $value === [] || $value === '';
    }
}
