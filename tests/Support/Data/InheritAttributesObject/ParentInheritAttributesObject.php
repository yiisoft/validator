<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Support\Data\InheritAttributesObject;

use Yiisoft\Validator\Rule\Equal;
use Yiisoft\Validator\Rule\Number;

class ParentInheritAttributesObject
{
    #[Number(min: 20)]
    #[Equal(24)]
    public int $age = 22;

    #[Equal(99)]
    public int $number = 15;

    #[Equal(7)]
    public int $level = 23;
}
