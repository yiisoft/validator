<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule\Image;

/**
 * {@link https://en.wikipedia.org/wiki/Aspect_ratio_(image)}
 */
final class ImageAspectRatio
{
    /**
     * @param int $width Expected width part for aspect ratio. For example, for `4:3` aspect ratio, it will be `4`.
     * @param int $height Expected height part for aspect ratio. For example, for `4:3` aspect ratio, it will be `3`.
     * @param float $margin Expected margin for aspect ratio in percents. For example, with value `1` and `4:3` aspect
     * ratio:
     *
     * - If the validated image has height of 600 pixels, the allowed width range is 794 - 806 pixels.
     * - If the validated image has width of 800 pixels, the allowed height range is 596 - 604 pixels.
     *
     * Defaults to `0` meaning no margin is allowed. For example, image with size 800 x 600 pixels and aspect ratio
     * expected to be `4:3` will meet this requirement.
     */
    public function __construct(
        private int $width,
        private int $height,
        private float $margin = 0,
    ) {
    }

    public function getWidth(): int
    {
        return $this->width;
    }

    public function getHeight(): int
    {
        return $this->height;
    }

    public function getMargin(): float
    {
        return $this->margin;
    }
}
