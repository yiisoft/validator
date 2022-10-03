<?php

declare(strict_types=1);

namespace Yiisoft\Validator\DataSet;

use Yiisoft\Validator\DataSetInterface;

final class ArrayDataSet implements DataSetInterface
{
    public function __construct(private array $data = [])
    {
    }

    public function getAttributeValue(string $attribute): mixed
    {
        return $this->data[$attribute] ?? null;
    }

    public function getData(): array
    {
        return $this->data;
    }
}
