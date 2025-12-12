<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Support\DataSet;

use Yiisoft\Validator\DataSetInterface;
use Yiisoft\Validator\RulesProviderInterface;

use function array_key_exists;

final class RulesProvidedDataSet implements RulesProviderInterface, DataSetInterface
{
    public function __construct(private array $data, private readonly array $rules) {}

    public function getPropertyValue(string $property): mixed
    {
        return $this->data[$property] ?? null;
    }

    public function getRules(): iterable
    {
        return $this->rules;
    }

    public function getData(): ?array
    {
        return $this->data;
    }

    public function hasProperty(string $property): bool
    {
        return array_key_exists($property, $this->data);
    }
}
