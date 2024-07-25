<?php

declare(strict_types=1);

namespace Yiisoft\Validator\PropertyTranslator;

use Yiisoft\Validator\PropertyTranslatorInterface;

/**
 * A property translator that returns property name as is without actually translating it.
 */
final class NullPropertyTranslator implements PropertyTranslatorInterface
{
    public function translate(string $property): string
    {
        return $property;
    }
}
