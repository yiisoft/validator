<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule\Image;

interface ImageInfoProviderInterface
{
    public function get(string $path): ?ImageInfo;
}
