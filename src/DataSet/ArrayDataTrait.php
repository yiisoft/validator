<?php

declare(strict_types=1);

namespace Yiisoft\Validator\DataSet;

/**
 * @internal
 */
trait ArrayDataTrait
{
    private array $data;

    public function getAttributeValue(string $attribute): mixed
    {
        return $this->data[$attribute] ?? null;
    }

    public function getData(): array
    {
        return $this->data;
    }
}
