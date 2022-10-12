<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Support\Data\InheritAttributesObject;

use Yiisoft\Validator\Rule\Equal;
use Yiisoft\Validator\Rule\Number;

class ParentInheritAttributesObject
{
    #[Number(min: 20)]
    public int $age = 17;

    #[Equal(targetValue: 99)]
    public int $number = 15;

    #[Equal(targetValue: 7)]
    public int $level = 23;
}
