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
     * @throws MissingAttributeException If there is no such value.
     *
     * @return mixed
     */
    public function getAttributeValue(string $attribute);

    public function getData(): mixed;
}
