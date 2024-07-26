<?php

declare(strict_types=1);

namespace Yiisoft\Validator\PropertyTranslator;

use Yiisoft\Validator\PropertyTranslatorInterface;

/**
 * A property translator that uses array of translations.
 */
final class ArrayPropertyTranslator implements PropertyTranslatorInterface
{
    /**
     * @param array $translations Translations array where each key is a property name and the corresponding value is
     * a translation.
     *
     * @psalm-param array<string,string> $translations
     */
    public function __construct(
        private array $translations,
    ) {
    }

    public function translate(string $property): string
    {
        return $this->translations[$property] ?? $property;
    }
}
