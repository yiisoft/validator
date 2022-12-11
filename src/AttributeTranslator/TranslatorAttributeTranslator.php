<?php

declare(strict_types=1);

namespace Yiisoft\Validator\AttributeTranslator;

use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Validator\AttributeTranslatorInterface;

/**
 * An attributes translator that used {@see TranslatorInterface} for translation.
 */
final class TranslatorAttributeTranslator implements AttributeTranslatorInterface
{
    public function __construct(
        private TranslatorInterface $translator,
    ) {
    }

    public function translate(string $attribute): string
    {
        return $this->translator->translate($attribute);
    }
}
