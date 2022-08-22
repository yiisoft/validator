<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Data\Charts;

use Yiisoft\Validator\Rule\Embedded;
use Yiisoft\Validator\Rule\Each;

final class Chart
{
    #[Each(
        rules: [
            new Embedded(),
        ],
    )]
    private array $points;
}
