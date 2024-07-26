<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

/**
 * Allows translating property names.
 */
interface PropertyTranslatorInterface
{
    /**
     * Translate property name.
     *
     * @param string $property The property name.
     *
     * @return string Translated property name.
     */
    public function translate(string $property): string;
}
