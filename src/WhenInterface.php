<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

use Closure;

/**
 * @psalm-type WhenType = null|Closure(mixed, ValidationContext):bool
 */
interface WhenInterface
{
    /**
     * @psalm-param WhenType $value
     */
    public function when(Closure|null $value): static;

    /**
     * @psalm-return WhenType
     */
    public function getWhen(): Closure|null;
}
