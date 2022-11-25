<?php

declare(strict_types=1);

namespace Yiisoft\Validator\EmptyHandler;

final class NullEmpty
{
    public function __invoke(mixed $value, bool $isAttributeMissing): bool
    {
        return $value === null;
    }
}
