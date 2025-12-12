<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule\Image;

final class ImageInfo
{
    public function __construct(
        private readonly int $width,
        private readonly int $height,
    ) {}

    public function getWidth(): int
    {
        return $this->width;
    }

    public function getHeight(): int
    {
        return $this->height;
    }
}
