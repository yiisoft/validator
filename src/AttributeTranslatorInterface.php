<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

/**
 * Allows translating attribute names.
 */
interface AttributeTranslatorInterface
{
    /**
     * Translate attribute name.
     *
     * @param string $attribute The attribute name.
     *
     * @return string Translated attribute name.
     */
    public function translate(string $attribute): string;
}
