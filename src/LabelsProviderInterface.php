<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

/**
 * Provides data attribute labels.
 */
interface LabelsProviderInterface
{
    /**
     * @return array<string, string> A set of attribute labels.
     */
    public function getValidationPropertyLabels(): array;
}
