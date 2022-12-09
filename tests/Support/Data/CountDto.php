<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Support\Data;

use Countable;
use Yiisoft\Validator\Rule\Count;

#[Count(min: 2)]
final class CountDto implements Countable
{
    public function __construct(
        private int $count = 0,
    ) {
    }

    public function count(): int
    {
        return $this->count;
    }
}
