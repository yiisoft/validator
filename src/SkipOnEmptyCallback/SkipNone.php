<?php

declare(strict_types=1);

namespace Yiisoft\Validator\SkipOnEmptyCallback;

use Yiisoft\Validator\ValidationContext;

final class SkipNone
{
    public function __invoke(mixed $value, object $rule, ValidationContext $context): bool
    {
        return false;
    }
}
