<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Support\Data\Charts;

use Yiisoft\Validator\Rule\Each;
use Yiisoft\Validator\Rule\Nested;

final class Chart
{
    #[Each([new Nested(Point::class)])]
    private readonly array $points;
}
