<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

/**
 * Provides optional implementation of {@see AttributeTranslatorInterface} for translation of attribute names.
 */
interface AttributeTranslatorProviderInterface
{
    /**
     * Get attribute translator instance to use.
     *
     * @return AttributeTranslatorInterface|null Attribute translator instance to use. If null is returned,
     * the validator uses default value configured.
     *
     * @see Validator::$defaultAttributeTranslator (can be configured via constructor).
     */
    public function getAttributeTranslator(): ?AttributeTranslatorInterface;
}
