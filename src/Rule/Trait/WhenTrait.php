<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule\Trait;

use Closure;
use Yiisoft\Validator\ValidationContext;

trait WhenTrait
{
    /**
     * @psalm-param Closure(mixed, ValidationContext):bool|null $value
     */
    public function when(?Closure $value): static
    {
        $new = clone $this;
        $new->when = $value;
        return $new;
    }

    /**
     * @psalm-return Closure(mixed, ValidationContext):bool|null
     */
    public function getWhen(): ?Closure
    {
        return $this->when;
    }
}
