<?php

declare(strict_types=1);

namespace Yiisoft\Validator\DataSet;

use Yiisoft\Validator\DataWrapperInterface;
use Yiisoft\Validator\Helper\DataSetNormalizer;

use function array_key_exists;

/**
 * A data set for storing data as an associative array, where keys are property names and values are their
 * corresponding values. An example of usage:
 *
 * ```php
 * $dataSet = new ArrayDataSet(['name' => 'John', 'age' => 18]);
 * ```
 *
 * When using validator, there is no need to wrap your data manually. Array will be automatically wrapped with
 * {@see ArrayDataSet} by {@see DataSetNormalizer} during validation.
 */
final class ArrayDataSet implements DataWrapperInterface
{
    public function __construct(
        /**
         * @var array A mapping between property names and their values.
         */
        private array $data = [],
    ) {
    }

    /**
     * Returns a property value by its name.
     *
     * Note that in case of non-existing property a default `null` value is returned. If you need to check the presence
     * of property or return a different default value, use {@see hasProperty()} instead.
     *
     * @param string $property Property name.
     *
     * @return mixed Property value.
     */
    public function getPropertyValue(string $property): mixed
    {
        return $this->data[$property] ?? null;
    }

    /**
     * A getter for {@see $data} property. Returns the validated data as a whole in a form of array.
     *
     * @return array A mapping between property names and their values.
     */
    public function getData(): array
    {
        return $this->data;
    }

    public function getSource(): array
    {
        return $this->data;
    }

    /**
     * Whether this data set has the property with a given name. Note that this means existence only and properties
     * with empty values are treated as present too.
     *
     * @param string $property Property name.
     *
     * @return bool Whether the property exists: `true` - exists and `false` - otherwise.
     */
    public function hasProperty(string $property): bool
    {
        return array_key_exists($property, $this->data);
    }
}
