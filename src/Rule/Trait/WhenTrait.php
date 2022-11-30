<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule\Trait;

use Closure;
use Yiisoft\Validator\WhenInterface;

/**
 * @psalm-import-type WhenType from WhenInterface
 */
trait WhenTrait
{
    /**
     * @psalm-param WhenType $value
     */
    public function when(Closure|null $value): static
    {
        $new = clone $this;
        $new->when = $value;
        return $new;
    }

    /**
     * @psalm-return WhenType
     */
    public function getWhen(): Closure|null
    {
        return $this->when;
    }
}
