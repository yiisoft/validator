<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

/**
 * Provides implementation of {@see AttributeTranslatorInterface} for translate attribute names.
 */
interface AttributeTranslatorProviderInterface
{
    public function getAttributeTranslator(): ?AttributeTranslatorInterface;
}
