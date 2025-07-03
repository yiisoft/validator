<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Support;

use Stringable;

final class StringableObject implements Stringable
{
    public function __construct(
        private string $value,
    ) {
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
