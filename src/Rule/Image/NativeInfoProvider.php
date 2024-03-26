<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule\Image;

final class NativeInfoProvider implements InfoProviderInterface
{
    public function get(string $path): ?Info
    {
        $data = @getimagesize($path);
        if ($data === false) {
            return null;
        }

        return new Info($data[0], $data[1]);
    }
}
