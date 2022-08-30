<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule\Trait;

use Closure;
use InvalidArgumentException;
use Yiisoft\Validator\SkipOnEmptyCallback\SkipOnEmpty;
use Yiisoft\Validator\ValidationContext;

use function is_callable;

trait BeforeValidationTrait
{
    /**
     * @var callable|null
     */
    private $skipOnEmptyCallback = null;

    public function shouldSkipOnEmpty(mixed $validatedValue): bool
    {
        return $this->skipOnEmptyCallback !== null && ($this->skipOnEmptyCallback)($validatedValue);
    }

    protected function setSkipOnEmptyCallback(mixed $skipOnEmpty = false): void
    {
        if ($skipOnEmpty === false) {
            $this->skipOnEmptyCallback = null;
            return;
        }

        if ($skipOnEmpty === true) {
            $this->skipOnEmptyCallback = new SkipOnEmpty();
            return;
        }

        if (!is_callable($skipOnEmpty)) {
            throw new InvalidArgumentException('$skipOnEmpty must be a boolean or a callable.');
        }

        $this->skipOnEmptyCallback = $skipOnEmpty;
    }

    public function skipOnEmpty(bool|callable $value): self
    {
        $new = clone $this;
        $new->setSkipOnEmptyCallback($value);
        return $new;
    }

    public function shouldSkipOnError(): bool
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
