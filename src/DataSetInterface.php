<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

/**
 * An interface for unifying access to different types of validated data. A class implementing it is called "data set".
 */
interface DataSetInterface
{
    /**
     * Returns an attribute value by its name.
     *
     * @param string $attribute Attribute name.
     *
     * @return mixed Attribute value.
     */
    public function getAttributeValue(string $attribute): mixed;

    /**
     * Returns the validated data as an associative array, where keys are attribute names and values are their
     * corresponding values. `null` means that implementation does not support getting an array of attributes.
     *
     * @return array|null Validated data as array of attributes or `null` when does not support this.
     */
    public function getData(): ?array;

    /**
     * Whether a data set has the attribute with a given name. Note that this means existence only and attributes with
     * empty values are treated as present.
     *
     * @param string $attribute Attribute name.
     *
     * @return bool Whether the attribute exists: `true` - exists and `false` - otherwise.
     */
    public function hasAttribute(string $attribute): bool;
}
