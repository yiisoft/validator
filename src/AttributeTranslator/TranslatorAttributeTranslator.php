<?php

declare(strict_types=1);

namespace Yiisoft\Validator\AttributeTranslator;

use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Validator\AttributeTranslatorInterface;

/**
 * An attribute translator that uses {@see TranslatorInterface}.
 */
final class TranslatorAttributeTranslator implements AttributeTranslatorInterface
{
    /**
     * @param TranslatorInterface $translator Translator to use.
     */
    public function __construct(
        private TranslatorInterface $translator,
    ) {
    }

    public function translate(string $attribute): string
    {
        return $this->translator->translate($attribute);
    }
}
