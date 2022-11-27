<?php

declare(strict_types=1);

namespace Yiisoft\Validator\EmptyCriteria;

final class WhenNull
{
    public function __invoke(mixed $value, bool $isAttributeMissing): bool
    {
        return $value === null;
    }
}
