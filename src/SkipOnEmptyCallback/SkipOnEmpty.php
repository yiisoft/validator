<?php

declare(strict_types=1);

namespace Yiisoft\Validator\SkipOnEmptyCallback;

use Yiisoft\Validator\ValidationContext;

final class SkipOnEmpty
{
    public function __invoke(mixed $value, object $rule, ValidationContext $context): bool
    {
        return $context->isAttributeMissing() || $value === null || $value === [] || $value === '';
    }
}
