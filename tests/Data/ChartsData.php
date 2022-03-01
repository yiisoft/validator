<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Data;

use Yiisoft\Validator\Attribute\HasMany;
use Yiisoft\Validator\Attribute\HasOne;
use Yiisoft\Validator\FormatterInterface;
use Yiisoft\Validator\Rule\Each;
use Yiisoft\Validator\Rule\Nested;
use Yiisoft\Validator\Rule\Number;

class ChartsData
{
    #[Each(incorrectInputMessage: 'Custom message 1.', message: 'Custom message 2.')]
    #[Nested(errorWhenPropertyPathIsNotFound: true, propertyPathIsNotFoundMessage: 'Custom message 3.')]
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
    #[Nested(errorWhenPropertyPathIsNotFound: true, propertyPathIsNotFoundMessage: 'Custom message 4.')]
    #[HasOne(Coordinates::class)]
    private $coordinates;
    #[Each(incorrectInputMessage: 'Custom message 5.', message: 'Custom message 6.')]
    #[Number(min: 0, max: 255)]
    private array $rgb;
}

class Coordinates
{
    #[Number(min: -10, max: 10, formatter: new CustomFormatter())]
    private int $x;
    #[Number(min: -10, max: 10)]
    private int $y;
}

final class CustomFormatter implements FormatterInterface
{
    public function format(string $message, array $parameters = []): string
    {
        return $message;
    }
}
