<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Stub;

use Yiisoft\Validator\DataSetInterface;

final class DataSet implements DataSetInterface
{
    public function __construct(private array $data = [])
    {
    }

    public function getAttributeValue(string $attribute): mixed
    {
        return $this->data[$attribute] ?? null;
    }

    public function getData(): mixed
    {
        return $this->data;
    }
}
