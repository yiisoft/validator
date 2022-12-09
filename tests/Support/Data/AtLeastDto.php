<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Support\Data;

use Yiisoft\Validator\Rule\AtLeast;

#[AtLeast(['a', 'b', 'c'])]
final class AtLeastDto
{
    public function __construct(
        public ?int $a = null,
        public ?int $b = null,
        public ?int $c = null,
    ) {
    }
}
