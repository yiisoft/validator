<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

/**
 * Provides data property labels.
 */
interface LabelsProviderInterface
{
    /**
     * @return array<string, string> A set of property labels.
     */
    public function getValidationPropertyLabels(): array;
}
