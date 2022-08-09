<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

final class SkipOnAll
{
    public function __invoke($value): bool
    {
        return false;
    }
}
