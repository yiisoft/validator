<?php

declare(strict_types=1);

namespace Yiisoft\Validator\AttributeTranslator;

use Yiisoft\Validator\AttributeTranslatorInterface;

/**
 * An attribute translator that uses array of translations.
 */
final class ArrayAttributeTranslator implements AttributeTranslatorInterface
{
    /**
     * @param array $translations Translations array where each key is an attribute name and the corresponding name is
     * a translation.
     */
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
