<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

interface DataSetInterface
{
    /**
     * Get specified attribute value.
     */
    public function getAttributeValue(string $attribute): mixed;

    public function getData(): mixed;

    /**
     * If there is such attribute in the set.
     */
    public function hasAttribute(string $attribute): bool;
}
