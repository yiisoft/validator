<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Stub\InheritAttributesObject;

use Yiisoft\Validator\Rule\Equal;
use Yiisoft\Validator\Rule\Number;

final class InheritAttributesObject extends ParentInheritAttributesObject
{
    #[Number(min: 21)]
    #[Equal(targetValue: 23)]
    public int $age = 18;

    public int $level = 2;
}
