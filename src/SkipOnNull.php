<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

final class SkipOnNull
{
    public function __invoke(mixed $value): bool
    {
        return $value === null;
    }
}
