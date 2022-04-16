<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

trait HandlerClassNameTrait
{
    public function getHandlerClassName(): string
    {
        return self::class . 'Handler';
    }
}
