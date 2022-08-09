<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

final class SkipOnNull
{
    public function __invoke($value): bool
    {
        return $value === null;
    }
}
