<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule\Image;

use Yiisoft\Validator\Rule\Image\ImageInfo;
use Yiisoft\Validator\Rule\Image\ImageInfoProviderInterface;

final class StubImageInfoProvider implements ImageInfoProviderInterface
{
    public function __construct(
        private readonly ?ImageInfo $info = null,
    ) {
    }

    public function get(string $path): ?ImageInfo
    {
        return $this->info;
    }
}
