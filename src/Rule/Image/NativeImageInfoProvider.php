<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule\Image;

final class NativeImageInfoProvider implements ImageInfoProviderInterface
{
    public function get(string $path): ?ImageInfo
    {
        $data = @getimagesize($path);
        if ($data === false) {
            return null;
        }

        return new ImageInfo($data[0], $data[1]);
    }
}
