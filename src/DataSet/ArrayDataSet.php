<?php

declare(strict_types=1);

namespace Yiisoft\Validator\DataSet;

use Yiisoft\Validator\DataSetInterface;
use Yiisoft\Validator\Exception\MissingAttributeException;

final class ArrayDataSet implements DataSetInterface
{
    private array $data;

    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    public function getRawAttributeValue(string $attribute)
    {
        if (!$this->hasAttribute($attribute)) {
            throw new MissingAttributeException("There is no \"$attribute\" key in the array.");
        }

        return $this->data[$attribute];
    }

    public function hasAttribute(string $attribute): bool
    {
        return isset($this->data[$attribute]);
    }
}
