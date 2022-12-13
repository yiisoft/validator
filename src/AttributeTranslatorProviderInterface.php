<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

/**
 * Provides implementation of {@see AttributeTranslatorInterface} for translations of attribute names.
 */
interface AttributeTranslatorProviderInterface
{
    public function getAttributeTranslator(): ?AttributeTranslatorInterface;
}
