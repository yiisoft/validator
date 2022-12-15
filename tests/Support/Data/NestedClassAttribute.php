<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Support\Data;

use Yiisoft\Validator\Rule\Nested;
use Yiisoft\Validator\Rule\Number;

#[Nested([
    'a' => new Number(min: 7),
    'b' => new Number(max: 1),
])]
final class NestedClassAttribute
{
    private int $a = 1;
    private int $b = 2;
}
