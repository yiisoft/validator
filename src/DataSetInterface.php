<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

/**
 * DataSetInterface represents a key-value data set.
 */
interface DataSetInterface
{
    /**
     * Get specified attribute value.
     *
     * @return mixed
     */
    public function getAttributeValue(string $attribute): mixed;

    public function getData(): mixed;

    /**
     * If there is such attribute in the set.
     */
    public function hasAttribute(string $attribute): bool;
}
