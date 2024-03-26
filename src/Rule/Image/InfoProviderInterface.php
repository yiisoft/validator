<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule\Image;

interface InfoProviderInterface
{
    public function get(string $path): ?Info;
}
