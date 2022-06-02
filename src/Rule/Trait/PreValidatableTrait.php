<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule\Trait;

use Closure;

trait PreValidatableTrait
{
    /**
     * @return bool
     */
    public function isSkipOnEmpty(): bool
    {
        return $this->skipOnEmpty;
    }

    /**
     * @return bool
     */
    public function isSkipOnError(): bool
    {
        return $this->skipOnError;
    }

    /**
     * @return Closure|null
     */
    public function getWhen(): ?Closure
    {
        return $this->when;
    }
}
