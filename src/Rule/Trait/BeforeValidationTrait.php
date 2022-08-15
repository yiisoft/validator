<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule\Trait;

use Closure;
use InvalidArgumentException;
use Yiisoft\Validator\SkipOnEmptyCallback\SkipNever;
use Yiisoft\Validator\SkipOnEmptyCallback\SkipOnEmpty;
use Yiisoft\Validator\ValidationContext;

use function is_callable;

trait BeforeValidationTrait
{
    public function getSkipOnEmpty(): bool
    {
        return $this->skipOnEmpty;
    }

    public function getSkipOnEmptyCallback(): callable
    {
        return $this->skipOnEmptyCallback;
    }

    public function shouldSkipOnEmpty(mixed $validatedValue): bool
    {
        return ($this->skipOnEmptyCallback)($validatedValue);
    }

    protected function initSkipOnEmptyProperties(bool $skipOnEmpty = false, ?callable $skipOnEmptyCallback = null): void
    {
        $this->skipOnEmpty = $skipOnEmpty;
        $this->skipOnEmptyCallback = $skipOnEmptyCallback;

        if ($this->skipOnEmptyCallback) {
            if (!is_callable($this->skipOnEmptyCallback)) {
                throw new InvalidArgumentException('$skipOnEmptyCallback must be a callable.');
            }

            $this->skipOnEmpty = true;

            return;
        }

        $this->skipOnEmptyCallback = $this->skipOnEmpty === false ? new SkipNever() : new SkipOnEmpty();
    }

    public function skipOnEmpty(bool $value): self
    {
        $new = clone $this;
        $new->skipOnEmpty = $value;
        $new->initSkipOnEmptyProperties($new->skipOnEmpty);

        return $new;
    }

    public function skipOnEmptyCallback(?callable $value): self
    {
        $new = clone $this;
        $new->skipOnEmptyCallback = $value;
        $new->initSkipOnEmptyProperties(false, $value);

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
