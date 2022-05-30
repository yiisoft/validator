<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Attribute;

use Attribute;

/**
 * Represents one-to-many relation.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final class HasMany
{
    public function __construct(string $relatedClassName)
    {
    }
}
