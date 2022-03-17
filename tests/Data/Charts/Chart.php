<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Data\Charts;

use Yiisoft\Validator\Attribute\HasMany;

final class Chart
{
    #[HasMany(Point::class)]
    private array $points;
}
