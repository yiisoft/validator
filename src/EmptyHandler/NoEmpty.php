<?php

declare(strict_types=1);

namespace Yiisoft\Validator\EmptyHandler;

final class NoEmpty
{
    public function __invoke(mixed $value, bool $isAttributeMissing): bool
    {
        return false;
    }
}
