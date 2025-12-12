<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Support\Data;

use Yiisoft\Validator\DataSetInterface;

use function array_key_exists;

final class ObjectWithDynamicDataSet implements DataSetInterface
{
    public function __construct(private readonly string $name) {}

    public function getPropertyValue(string $property): mixed
    {
        return $this->getData()[$property] ?? null;
    }

    public function getData(): ?array
    {
        return ['name' => $this->name];
    }

    public function hasProperty(string $property): bool
    {
        return array_key_exists($property, $this->getData());
    }
}
