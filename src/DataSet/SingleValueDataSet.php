<?php

declare(strict_types=1);

namespace Yiisoft\Validator\DataSet;

use BadMethodCallException;
use Yiisoft\Validator\DataSetInterface;

/**
 * Used for a single value of any (mixed) data type. Does not support attributes.
 */
final class SingleValueDataSet implements DataSetInterface
{
    public function __construct(private mixed $value)
    {
    }

    public function getAttributeValue(string $attribute): mixed
    {
        throw new BadMethodCallException('Single value data set does not support attributes.');
    }

    public function getData(): mixed
    {
        return $this->value;
    }

    public function hasAttribute(string $attribute): bool
    {
        throw new BadMethodCallException('Single value data set does not support attributes.');
    }
}
