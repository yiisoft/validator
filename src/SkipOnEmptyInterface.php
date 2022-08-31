<?php
declare(strict_types=1);

namespace Yiisoft\Validator;

interface SkipOnEmptyInterface
{
    public function skipOnEmpty(bool|callable $value): static;

    public function getSkipOnEmpty(): bool|callable;
}
