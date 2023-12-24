<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Helper;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class Label
{
    public function __construct(
        private string $label,
    ) {
    }

    public function getLabel(): string
    {
        return $this->label;
    }
}
