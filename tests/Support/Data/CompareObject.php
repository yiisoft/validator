<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Support\Data;

final class CompareObject
{
    public function __construct(
        private readonly mixed $a,
        private readonly mixed $b,
    ) {}
}
