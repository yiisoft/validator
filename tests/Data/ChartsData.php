<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Data;

use Yiisoft\Validator\Attribute\HasMany;
use Yiisoft\Validator\Attribute\HasOne;
use Yiisoft\Validator\Attribute\Validate;
use Yiisoft\Validator\Rule\Each;
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
    #[Validate(Each::class)]
    #[Validate(Number::class, ['min' => 0, 'max' => 255, 'skipOnError' => false])]
    private array $rgb;
}

class Coordinates
{
    #[Validate(Number::class, ['min' => -10, 'max' => 10])]
    private int $x;
    #[Validate(Number::class, ['min' => -10, 'max' => 10])]
    private int $y;
}
