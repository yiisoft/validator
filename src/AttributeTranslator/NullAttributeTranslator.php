<?php

declare(strict_types=1);

namespace Yiisoft\Validator\AttributeTranslator;

use Yiisoft\Validator\AttributeTranslatorInterface;

final class NullAttributeTranslator implements AttributeTranslatorInterface
{
    public function translate(string $attribute): string
    {
        return $attribute;
    }
}
