<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Support\Data\Charts;

use Yiisoft\Validator\Rule\Number;

final class Coordinate
{
    #[Number(min: -10, max: 10)]
    private int $x;
    #[Number(min: -10, max: 10)]
    private int $y;
}
