<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule\Trait;

use function is_bool;

trait SkipOnEmptyTrait
{
    public function skipOnEmpty(bool|callable|null $value): static
    {
        $new = clone $this;
        $new->skipOnEmpty = $value;
        return $new;
    }

    public function getSkipOnEmpty(): bool|callable|null
    {
        return $this->skipOnEmpty;
    }

    private function getSkipOnEmptyOption(): bool
    {
        if (is_bool($this->skipOnEmpty)) {
            return $this->skipOnEmpty;
        }

        if ($this->skipOnEmpty === null) {
            return false;
        }

        return true;
    }
}
