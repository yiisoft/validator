<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class HasOne
{
    public function __construct(string $relatedClassName)
    {
    }
}
