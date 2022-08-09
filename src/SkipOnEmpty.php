<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

final class SkipOnEmpty
{
    public function __invoke($value): bool
    {
        return $value === null || $value === [] || $value === '';
    }
}
