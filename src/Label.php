<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class Label
{
    public function __construct(
        private readonly string $label,
    ) {
    }

    public function getLabel(): string
    {
        return $this->label;
    }
}
