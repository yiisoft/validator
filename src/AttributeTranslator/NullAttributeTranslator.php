<?php

declare(strict_types=1);

namespace Yiisoft\Validator\AttributeTranslator;

use Yiisoft\Validator\AttributeTranslatorInterface;

/**
 * An attribute translator that returns attribute name as is.
 */
final class NullAttributeTranslator implements AttributeTranslatorInterface
{
    public function translate(string $attribute): string
    {
        return $attribute;
    }
}
