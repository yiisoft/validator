<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

use Yiisoft\Validator\Exception\MissingAttributeException;

/**
 * DataSetInterface represents a key-value data set
 */
interface DataSetInterface
{
    /**
     * Get specified attribute value for raw data.
     *
     * @throws MissingAttributeException if there is no such value
     *
     * @return mixed Raw attribute value that is set to the form with no type-casting performed.
     */
    public function getRawAttributeValue(string $attribute);

    /**
     * If there is such attribute in the set
     *
     * @return bool
     */
    public function hasAttribute(string $attribute): bool;
}
