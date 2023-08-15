<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Support\Data;

use Yiisoft\Validator\Rule\OneOf;

#[OneOf(['a', 'b', 'c'])]
final class OneOfDto
{
    public function __construct(
        public ?int $a = null,
        public ?int $b = null,
        public ?int $c = null,
    ) {
    }
}
