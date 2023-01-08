<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Support\DataSet;

use Yiisoft\Validator\DataSetInterface;
use Yiisoft\Validator\RulesProviderInterface;

use function array_key_exists;

final class RulesProvidedDataSet implements RulesProviderInterface, DataSetInterface
{
    public function __construct(private array $data, private array $rules)
    {
    }

    public function getAttributeValue(string $attribute): mixed
    {
        return $this->data[$attribute] ?? null;
    }

    public function getRules(): iterable
    {
        return $this->rules;
    }

    public function getData(): ?array
    {
        return $this->data;
    }

    public function hasAttribute(string $attribute): bool
    {
        return array_key_exists($attribute, $this->data);
    }
}
