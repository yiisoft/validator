<?php

declare(strict_types=1);

namespace Yiisoft\Validator\EmptyCriteria;

final class Never
{
    public function __invoke(mixed $value, bool $isAttributeMissing): bool
    {
        return false;
    }
}
