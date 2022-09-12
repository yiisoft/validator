<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule\Trait;

use Closure;
use Yiisoft\Validator\ValidationContext;

trait BeforeValidationTrait
{
    public function skipOnError(bool $value): static
    {
        $new = clone $this;
        $new->skipOnError = $value;
        return $new;
    }

    public function shouldSkipOnError(): bool
    {
        return $this->skipOnError;
    }

    /**
     * @psalm-return Closure(mixed, ValidationContext):bool|null
     */
    public function getWhen(): ?Closure
    {
        return $this->when;
    }
}
