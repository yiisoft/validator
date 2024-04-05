<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule\Image;

/**
 * > PHP native function `getimagesize()` don't support HEIF / HEIC formats.
 */
final class NativeImageInfoProvider implements ImageInfoProviderInterface
{
    public function get(string $path): ?ImageInfo
    {
        /**
         * @psalm-var (array{0:int,1:int}&array)|false $data Need for PHP 8.0 only
         */
        $data = @getimagesize($path);
        if ($data === false) {
            return null;
        }

        return new ImageInfo($data[0], $data[1]);
    }
}
