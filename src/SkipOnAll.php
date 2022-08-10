<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

final class SkipOnAll
{
    public function __invoke(mixed $value): bool
    {
        return false;
    }
}
