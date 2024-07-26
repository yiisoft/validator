<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule\Trait;

use Yiisoft\Validator\ValidationContext;

trait TranslatedPropertiesHandlerTrait
{
    /**
     * @param string[] $properties
     */
    private function getFormattedPropertiesString(array $properties, ValidationContext $context): string
    {
        return '"' . implode('", "', $this->getTranslatedProperties($properties, $context, false)) . '"';
    }

    /**
     * @param string[] $properties
     */
    private function getCapitalizedPropertiesString(array $properties, ValidationContext $context): string
    {
        return '"' . implode('", "', $this->getTranslatedProperties($properties, $context, true)) . '"';
    }

    /**
     * @param string[] $properties
     *
     * @return string[]
     */
    private function getTranslatedProperties(array $properties, ValidationContext $context, bool $capitalized): array
    {
        $initialLabel = $context->getPropertyLabel();
        $translatedProperties = [];
        foreach ($properties as $property) {
            $context->setPropertyLabel($property);
            $translatedProperties[] = $capitalized
                ? $context->getCapitalizedTranslatedProperty()
                : $context->getTranslatedProperty();
        }

        /** @var string[] $translatedProperties */

        $context->setPropertyLabel($initialLabel);

        return $translatedProperties;
    }
}
