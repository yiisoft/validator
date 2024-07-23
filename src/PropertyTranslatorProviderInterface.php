<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

/**
 * Provides optional implementation of {@see PropertyTranslatorInterface} for translation of property names.
 */
interface PropertyTranslatorProviderInterface
{
    /**
     * Get property translator instance to use.
     *
     * @return PropertyTranslatorInterface|null Property translator instance to use. If null is returned,
     * the validator uses default value configured.
     *
     * @see Validator::$defaultPropertyTranslator (can be configured via constructor).
     */
    public function getPropertyTranslator(): ?PropertyTranslatorInterface;
}
