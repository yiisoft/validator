<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule\Trait;

trait SkipOnErrorTrait
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
}
