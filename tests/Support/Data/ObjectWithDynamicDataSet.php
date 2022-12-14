<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Support\Data;

use Yiisoft\Validator\DataSetInterface;

use function array_key_exists;

final class ObjectWithDynamicDataSet implements DataSetInterface
{
    public function __construct(private string $name)
    {
    }

    public function getAttributeValue(string $attribute): mixed
    {
        return $this->getData()[$attribute] ?? null;
    }

    public function getData(): ?array
    {
        return ['name' => $this->name];
    }

    public function getSource(): self
    {
        return $this;
    }

    public function hasAttribute(string $attribute): bool
    {
        return array_key_exists($attribute, $this->getData());
    }
}
