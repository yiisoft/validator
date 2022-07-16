<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Data\Charts;

use Yiisoft\Validator\Attribute\Embedded;
use Yiisoft\Validator\Rule\Count;
use Yiisoft\Validator\Rule\Each;
use Yiisoft\Validator\Rule\Number;

final class Point
{
    #[Each(
        rules: [
            new Embedded(Coordinate::class),
        ],
    )]
    private $coordinates;
    #[Count(exactly: 3)]
    #[Each(
        rules: [
            new Number(min: 0, max: 255),
        ],
        incorrectInputMessage: 'Custom message 5.',
        message: 'Custom message 6.',
    )]
    private array $rgb;
}
