<?php

declare(strict_types=1);

namespace Yiisoft\Validator\DataSet;

use Yiisoft\Validator\DataSetInterface;

use function array_key_exists;

final class ArrayDataSet implements DataSetInterface
{
    public function __construct(private array $data = [])
    {
    }

    public function getAttributeValue(string $attribute): mixed
    {
        return $this->hasAttribute($attribute) ? $this->data[$attribute] : null;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function hasAttribute(string $attribute): bool
    {
        return array_key_exists($attribute, $this->data);
    }
}
