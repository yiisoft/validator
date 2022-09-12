<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

use Closure;

/**
 * `BeforeValidationInterface` is an interface implemented by rules that need to execute checks before the validation.
 */
interface BeforeValidationInterface
{
    public function skipOnError(bool $value): static;

    public function shouldSkipOnError(): bool;

    /**
     * @psalm-param Closure(mixed, ValidationContext):bool|null $value
     */
    public function when(?Closure $value): static;

    /**
     * @psalm-return Closure(mixed, ValidationContext):bool|null
     */
    public function getWhen(): ?Closure;
}
