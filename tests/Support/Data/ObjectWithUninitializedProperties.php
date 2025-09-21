<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Support\Data;

use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Rule\Required;

final class ObjectWithUninitializedProperties
{
    #[Required]
    public string $name;

    #[Number(min: 22)]
    protected int $age;

    #[Number(max: 100)]
    private int $number = 42;
}
