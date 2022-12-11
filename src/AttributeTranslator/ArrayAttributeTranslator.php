<?php

declare(strict_types=1);

namespace Yiisoft\Validator\AttributeTranslator;

use Yiisoft\Validator\AttributeTranslatorInterface;

/**
 * An attributes translator that used array of translations for translate attribute name.
 */
final class ArrayAttributeTranslator implements AttributeTranslatorInterface
{
    public function __construct(
        /**
         * @var array<string,string>
         */
        private array $translations,
    ) {
    }

    public function translate(string $attribute): string
    {
        return $this->translations[$attribute] ?? $attribute;
    }
}
