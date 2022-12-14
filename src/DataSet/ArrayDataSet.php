<?php

declare(strict_types=1);

namespace Yiisoft\Validator\DataSet;

use Yiisoft\Validator\DataSetInterface;

use Yiisoft\Validator\Helper\DataSetNormalizer;

use function array_key_exists;

/**
 * A data set for storing data as an associative array, where keys are attribute names and values are their
 * corresponding values. An example of usage:
 *
 * ```php
 * $dataSet = new ArrayDataSet(['name' => 'John', 'age' => 18]);
 * ```
 *
 * When using validator, there is no need to wrap your data manually. Array will be automatically wrapped with
 * {@see ArrayDataSet} by {@see DataSetNormalizer} during validation.
 */
final class ArrayDataSet implements DataSetInterface
{
    public function __construct(
        /**
         * @var array A mapping between attribute names and their values.
         */
        private array $data = [],
    ) {
    }

    /**
     * Returns an attribute value by its name.
     *
     * Note that in case of non-existing attribute a default `null` value is returned. If you need to check the presence
     * of attribute or return a different default value, use {@see hasAttribute()} instead.
     *
     * @param string $attribute Attribute name.
     *
     * @return mixed Attribute value.
     */
    public function getAttributeValue(string $attribute): mixed
    {
        return $this->data[$attribute] ?? null;
    }

    /**
     * A getter for {@see $data} property. Returns the validated data as a whole in a form of array.
     *
     * @return array A mapping between attribute names and their values.
     */
    public function getData(): ?array
    {
        return $this->data;
    }

    public function getSource(): array
    {
        return $this->data;
    }

    /**
     * Whether this data set has the attribute with a given name. Note that this means existence only and attributes
     * with empty values are treated as present too.
     *
     * @param string $attribute Attribute name.
     *
     * @return bool Whether the attribute exists: `true` - exists and `false` - otherwise.
     */
    public function hasAttribute(string $attribute): bool
    {
        return array_key_exists($attribute, $this->data);
    }
}
