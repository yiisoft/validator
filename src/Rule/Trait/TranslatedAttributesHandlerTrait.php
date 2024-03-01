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
        return '"' . implode('", "', $this->getTranslatedAttributes($attributes, $context, false)) . '"';
    }

    /**
     * @param string[] $attributes
     */
    private function getCapitalizedAttributesString(array $attributes, ValidationContext $context): string
    {
        return '"' . implode('", "', $this->getTranslatedAttributes($attributes, $context, true)) . '"';
    }

    /**
     * @param string[] $attributes
     *
     * @return string[]
     */
    private function getTranslatedAttributes(array $attributes, ValidationContext $context, bool $capitalized): array
    {
        $initialLabel = $context->getAttributeLabel();
        $translatedAttributes = [];
        foreach ($attributes as $attribute) {
            $context->setAttributeLabel($attribute);
            $translatedAttributes[] = $capitalized
                ? $context->getCapitalizedTranslatedAttribute()
                : $context->getTranslatedAttribute();
        }

        /** @var string[] $translatedAttributes */

        $context->setAttributeLabel($initialLabel);

        return $translatedAttributes;
    }
}
