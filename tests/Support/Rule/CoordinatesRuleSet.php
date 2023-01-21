<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Support\Rule;

use Yiisoft\Validator\Rule\Composite;
use Yiisoft\Validator\Rule\Number;

final class CoordinatesRuleSet extends Composite
{
    public function getRules(): iterable
    {
        return [
            'latitude' => new Number(min: -90, max: 90),
            'longitude' => new Number(min: -180, max: 180),
        ];
    }
}
