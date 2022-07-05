<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Attribute;

use Attribute;

/**
 * TODO: rename to better understanding
 * Represents one-to-one relation.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final class HasOne
{
    public function __construct(string $relatedClassName)
    {
    }
}
