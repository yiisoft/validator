<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Support\Data;

use Yiisoft\Validator\DataSetInterface;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Rule\Required;

use function array_key_exists;

final class ObjectWithDataSet implements DataSetInterface
{
    #[Required]
    public string $name = '';

    #[Number(min: 21)]
    protected int $age = 17;

    #[Number(max: 100)]
    private int $number = 42;

    public function getPropertyValue(string $property): mixed
    {
        return $this->getData()[$property] ?? null;
    }

    public function getData(): ?array
    {
        return ['key1' => 7, 'key2' => 42];
    }

    public function hasProperty(string $property): bool
    {
        return array_key_exists($property, $this->getData());
    }
}
