<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

use Closure;

interface WhenInterface
{
    /**
     * @param Closure(mixed, ValidationContext):bool|null $value
     */
    public function when(?Closure $value): static;

    /**
     * @return Closure(mixed, ValidationContext):bool|null
     */
    public function getWhen(): ?Closure;
}
