<?php

declare(strict_types=1);

namespace Yiisoft\Validator\DataSet;

use Yiisoft\Validator\DataSetInterface;
use Yiisoft\Validator\DataWrapperInterface;

/**
 * A data set used for a single value of any (mixed) data type. Does not support attributes.
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
     * Returns an attribute value by its name. {@see SingleValueDataSet} does not support attributes so `null` is always
     * returned regardless of the attribute name.
     *
     * @param string $attribute Attribute name.
     *
     * @return mixed `null` value indicating that attributes are not supported.
     */
    public function getAttributeValue(string $attribute): mixed
    {
        return null;
    }

    /**
     * A getter for {@see $data} property. Returns the validated data as a whole. In this case the single value itself
     * is returned as a data because it can not be decoupled.
     *
     * @return mixed Single value of any (mixed) data type.
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
     * Whether this data set has the attribute with a given name.
     *
     * @param string $attribute Attribute name.
     *
     * @return bool `false` value indicating that attributes are not supported.
     */
    public function hasAttribute(string $attribute): bool
    {
        return false;
    }
}
