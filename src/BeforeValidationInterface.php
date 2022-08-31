<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

use Closure;

/**
 * `BeforeValidationInterface` is the interface implemented by rules that need to execute checks before the validation.
 */
interface BeforeValidationInterface
{
    public function skipOnEmpty(bool|callable $value): static;

    public function getSkipOnEmpty(): bool|callable;

    public function shouldSkipOnError(): bool;

    /**
     * @psalm-return Closure(mixed, ValidationContext):bool|null
     *
     * @return Closure|null
     */
    public function getWhen(): ?Closure;
}
