<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Data\Charts;

use Yiisoft\Validator\Rule\Each;
use Yiisoft\Validator\Rule\Nested;

final class Chart
{
    #[Each(
        rules: [
            new Nested(Point::class),
        ],
    )]
    private array $points;
}
