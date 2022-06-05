<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

use Closure;

interface PreValidatableRuleInterface
{
    public function isSkipOnEmpty(): bool;

    public function isSkipOnError(): bool;

    /**
     * @psalm-return Closure(mixed, ValidationContext):bool|null
     *
     * @return Closure|null
     */
    public function getWhen(): ?Closure;
}
