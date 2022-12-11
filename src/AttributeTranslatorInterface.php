<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

interface AttributeTranslatorInterface
{
    public function translate(string $attribute): string;
}
