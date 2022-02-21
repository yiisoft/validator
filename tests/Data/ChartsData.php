<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Data;

use Yiisoft\Validator\Attribute\HasMany;
use Yiisoft\Validator\Attribute\HasOne;
use Yiisoft\Validator\Rule\Number;

class ChartsData
{
    #[HasMany(Chart::class)]
    private array $charts;
}

class Chart
{
    #[HasMany(Point::class)]
    private array $points;
}

class Point
{
    #[HasOne(Coordinates::class)]
    private $coordinates;
    #[Number(min: 0, max: 255)]
    private array $rgb;
}

class Coordinates
{
    #[Number(min: -10, max: 10)]
    private int $x;
    #[Number(min: -10, max: 10)]
    private int $y;
}
