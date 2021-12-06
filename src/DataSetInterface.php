<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

use Yiisoft\Validator\Exception\MissingAttributeException;

/**
 * DataSetInterface represents a key-value data set.
 */
interface DataSetInterface
{
    /**
     * Get specified attribute value.
     *
     * @throws MissingAttributeException if there is no such value.
     *
     * @return mixed
     */
    public function getAttributeValue(string $attribute);

    /**
     * If there is such attribute in the set.
     *
     * @param string $attribute
     *
     * @return bool
     */
    public function hasAttribute(string $attribute): bool;
}
