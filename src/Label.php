<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY|Attribute::TARGET_CLASS)]
final class Label
{
    public function __construct(
        private ?string $label = null,
    ) {
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }
}
