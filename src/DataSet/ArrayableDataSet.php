<?php

declare(strict_types=1);

namespace Yiisoft\Validator\DataSet;

use Yiisoft\Validator\DataSetInterface;
use Yiisoft\Validator\Exception\MissingAttributeException;

final class ArrayableDataSet implements DataSetInterface
{
    private array $data;

    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    public function getAttributeValue(string $attribute)
    {
        if (!$this->hasAttribute($attribute)) {
            throw new MissingAttributeException("There is no \"$attribute\" attribute in the class.");
        }

        return $this->data[$attribute];
    }

    public function hasAttribute(string $attribute): bool
    {
        return isset($this->data[$attribute]);
    }
}
