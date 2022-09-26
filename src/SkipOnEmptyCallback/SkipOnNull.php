<?php

declare(strict_types=1);

namespace Yiisoft\Validator\SkipOnEmptyCallback;

use Yiisoft\Validator\ValidationContext;

final class SkipOnNull
{
    public function __invoke(mixed $value, object $rule, ValidationContext $context): bool
    {
        return $value === null;
    }
}
