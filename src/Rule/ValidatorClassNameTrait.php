<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

trait ValidatorClassNameTrait
{
    public function getValidatorClassName(): string
    {
        return self::class . 'Validator';
    }
}
