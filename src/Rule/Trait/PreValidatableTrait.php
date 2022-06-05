<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule\Trait;

use Closure;
use Yiisoft\Validator\ValidationContext;

trait PreValidatableTrait
{
    public function isSkipOnEmpty(): bool
    {
        return $this->skipOnEmpty;
    }

    public function isSkipOnError(): bool
    {
        return $this->skipOnError;
    }

    /**
     * @psalm-return Closure(mixed, ValidationContext):bool|null
     *
     * @return Closure|null
     */
    public function getWhen(): ?Closure
    {
        return $this->when;
    }
}
