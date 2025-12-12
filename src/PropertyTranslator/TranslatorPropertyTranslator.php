<?php

declare(strict_types=1);

namespace Yiisoft\Validator\PropertyTranslator;

use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Validator\PropertyTranslatorInterface;

/**
 * A property translator that uses {@see TranslatorInterface}.
 */
final class TranslatorPropertyTranslator implements PropertyTranslatorInterface
{
    /**
     * @param TranslatorInterface $translator Translator to use.
     */
    public function __construct(
        private readonly TranslatorInterface $translator,
    ) {}

    public function translate(string $property): string
    {
        return $this->translator->translate($property);
    }
}
