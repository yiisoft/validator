<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Stub;

use Yiisoft\Validator\DataSetInterface;
use Yiisoft\Validator\RulesProviderInterface;

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

    public function getData(): mixed
    {
        return $this->data;
    }
}
