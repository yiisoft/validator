<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

/**
 * An interface for unifying access to different types of validated data. A class implementing it is called "data set".
 */
interface DataSetInterface
{
    /**
     * Returns a property value by its name.
     *
     * @param string $property Property name.
     *
     * @return mixed Property value.
     */
    public function getPropertyValue(string $property): mixed;

    /**
     * Returns the validated data as an associative array, where keys are property names and values are their
     * corresponding values. `null` means that implementation does not support getting an array of properties.
     *
     * @return array|null Validated data as array of properties or `null` when does not support this.
     */
    public function getData(): ?array;

    /**
     * Whether a data set has the property with a given name. Note that this means existence only and properties with
     * empty values are treated as present.
     *
     * @param string $property Property name.
     *
     * @return bool Whether the property exists: `true` - exists and `false` - otherwise.
     */
    public function hasProperty(string $property): bool;
}
