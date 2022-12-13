<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

/**
 * An interface for translates an attributes names.
 */
interface AttributeTranslatorInterface
{
    /**
     * @param string $attribute The attribute name.
     *
     * @return string Translated attribute name.
     */
    public function translate(string $attribute): string;
}
