<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

final class SkipNever
{
    public function __invoke(mixed $value): bool
    {
        return false;
    }
}
