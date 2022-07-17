<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule\Trait;

trait HandlerClassNameTrait
{
    public function getHandlerClassName(): string
    {
        return static::class . 'Handler';
    }
}
