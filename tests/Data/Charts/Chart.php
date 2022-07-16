<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Data\Charts;

use Yiisoft\Validator\Attribute\Embedded;
use Yiisoft\Validator\Rule\Each;

final class Chart
{
    #[Each(
        rules: [
            new Embedded(Point::class),
        ],
    )]
    private array $points;
}
