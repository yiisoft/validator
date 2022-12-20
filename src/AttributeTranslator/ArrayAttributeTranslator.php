<?php

declare(strict_types=1);

namespace Yiisoft\Validator\AttributeTranslator;

use Yiisoft\Validator\AttributeTranslatorInterface;

/**
 * An attribute translator that uses array of translations.
 */
final class ArrayAttributeTranslator implements AttributeTranslatorInterface
{
    public function __construct(
        /**
         * @var array Translations array where each key is an attribute name and the corresponding value is
         * a translation.
         * @psalm-var array<string,string>
         */
        private array $translations,
    ) {
    }

    public function translate(string $attribute): string
    {
        return $this->translations[$attribute] ?? $attribute;
    }
}
