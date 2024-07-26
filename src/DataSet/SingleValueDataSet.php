<?php

declare(strict_types=1);

namespace Yiisoft\Validator\DataSet;

use Yiisoft\Validator\DataWrapperInterface;

/**
 * A data set used for a single value of any (mixed) data type. Does not support PHP attributes.
 *
 * Examples of usage:
 *
 * ```php
 * $dataSet = new SingleValueDataSet(5);
 * $dataSet = new SingleValueDataSet(2.5);
 * $dataSet = new SingleValueDataSet('text');
 * $dataSet = new SingleValueDataSet(null);
 * $dataSet = new SingleValueDataSet(false);
 * $dataSet = new SingleValueDataSet([]);
 * $dataSet = new SingleValueDataSet(new \stdClass());
 * ```
 *
 * When using validator, there is no need to wrap data manually. Such types be automatically wrapped with
 * {@see SingleValueDataSet} by {@see DataSetNormalizer} during validation.
 *
 * For arrays and objects {@see ArrayDataSet} and {@see ObjectDataSet} can be used accordingly.
 */
final class SingleValueDataSet implements DataWrapperInterface
{
    public function __construct(
        /**
         * @var mixed Single value of any (mixed) data type.
         */
        private mixed $value,
    ) {
    }

    /**
     * Returns a property value by its name. {@see SingleValueDataSet} does not support properties so `null` is always
     * returned regardless of the property name.
     *
     * @param string $property Property name.
     *
     * @return mixed `null` value indicating that properties are not supported.
     */
    public function getPropertyValue(string $property): mixed
    {
        return null;
    }

    /**
     * Returns the validated data as array.
     */
    public function getData(): ?array
    {
        return null;
    }

    public function getSource(): mixed
    {
        return $this->value;
    }

    /**
     * Whether this data set has the property with a given name.
     *
     * @param string $property Property name.
     *
     * @return bool `false` value indicating that properties are not supported.
     */
    public function hasProperty(string $property): bool
    {
        return false;
    }
}
