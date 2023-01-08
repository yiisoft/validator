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

    public function getAttributeValue(string $attribute): mixed
    {
        return $this->getData()[$attribute] ?? null;
    }

    public function getData(): ?array
    {
        return ['key1' => 7, 'key2' => 42];
    }

    public function hasAttribute(string $attribute): bool
    {
        return array_key_exists($attribute, $this->getData());
    }
}
