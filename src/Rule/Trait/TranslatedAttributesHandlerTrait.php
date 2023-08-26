<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule\Trait;

use Yiisoft\Validator\ValidationContext;

trait TranslatedAttributesHandlerTrait
{
    /**
     * @param string[] $attributes
     */
    private function getFormattedAttributesString(array $attributes, ValidationContext $context): string
    {
        return '"' . implode('", "', $this->getTranslatedAttributes($attributes, $context)) . '"';
    }

    /**
     * @param string[] $attributes
     *
     * @return string[]
     */
    private function getTranslatedAttributes(array $attributes, ValidationContext $context): array
    {
        $initialAttribute = $context->getAttribute();
        $translatedAttributes = [];
        foreach ($attributes as $attribute) {
            $translatedAttributes[] = $context->setAttribute($attribute)->getTranslatedAttribute();
        }

        /** @var string[] $translatedAttributes */

        $context->setAttribute($initialAttribute);

        return $translatedAttributes;
    }
}
