<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Support\Data\Charts;

use Yiisoft\Validator\Rule\Number;

final class Coordinates
{
    #[Number(min: -10, max: 10)]
    private readonly int $x;
    #[Number(min: -10, max: 10)]
    private readonly int $y;
}
