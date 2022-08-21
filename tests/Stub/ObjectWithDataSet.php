<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Stub;

use Yiisoft\Validator\DataSetInterface;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Rule\Required;

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
        return $this->getObjectData()[$attribute] ?? null;
    }

    public function getData(): mixed
    {
        return $this->getObjectData();
    }

    private function getObjectData(): array
    {
        return ['key1' => 7, 'key2' => 42];
    }
}
