<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Support\DataSet;

use Yiisoft\Validator\DataSetInterface;

use function array_key_exists;

final class SimpleDataSet implements DataSetInterface
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

    public function hasAttribute(string $attribute): bool
    {
        return array_key_exists($attribute, $this->data);
    }
}
