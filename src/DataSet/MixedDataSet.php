<?php

declare(strict_types=1);

namespace Yiisoft\Validator\DataSet;

use Yiisoft\Validator\DataSetInterface;

/**
 * Used for a single value of any (mixed) data type. Does not support attributes.
 */
final class MixedDataSet implements DataSetInterface
{
    public function __construct(private mixed $value)
    {
    }

    public function getAttributeValue(string $attribute): mixed
    {
        return null;
    }

    public function getData(): mixed
    {
        return $this->value;
    }

    public function hasAttribute(string $attribute): bool
    {
        return true;
    }
}
