<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

/**
 * Provides optional implementation of {@see AttributeTranslatorInterface} for translation of attribute names.
 */
interface AttributeTranslatorProviderInterface
{
    /**
     * @return AttributeTranslatorInterface|null Attribute translator instance to use. If null is returned,
     * the validator uses default value configured.
     *
     * @see Validator constructor, `$defaultAttributeTranslator` argument.
     */
    public function getAttributeTranslator(): ?AttributeTranslatorInterface;
}
