<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule\Trait;

trait SkipOnEmptyTrait
{
    public function skipOnEmpty(bool|callable $value): static
    {
        $new = clone $this;
        $new->skipOnEmpty = $value;
        return $new;
    }

    public function getSkipOnEmpty(): bool|callable
    {
        return $this->skipOnEmpty;
    }
}
