<?php

declare(strict_types=1);

namespace Yiisoft\Validator\SkipOnEmptyCallback;

final class SkipNone
{
    public function __invoke(mixed $value, bool $isAttributeMissing): bool
    {
        return false;
    }
}
