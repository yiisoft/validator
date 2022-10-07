<?php
declare(strict_types=1);

namespace Yiisoft\Validator;

interface SkipOnErrorInterface
{
    public function skipOnError(bool $value): static;

    public function shouldSkipOnError(): bool;
}
