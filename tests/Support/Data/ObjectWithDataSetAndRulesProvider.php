<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Support\Data;

use Yiisoft\Validator\DataSetInterface;
use Yiisoft\Validator\Rule\Equal;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\RulesProviderInterface;

use function array_key_exists;

final class ObjectWithDataSetAndRulesProvider implements DataSetInterface, RulesProviderInterface
{
    #[Required]
    public string $name = '';

    #[Number(min: 21)]
    protected int $key1 = 17;

    #[Number(max: 100)]
    private int $key2 = 42;

    public function getRules(): iterable
    {
        return [
            'key1' => [new Required()],
            'key2' => [new Required(), new Equal(99)],
        ];
    }

    public function getPropertyValue(string $property): mixed
    {
        return $this->getObjectData()[$property] ?? null;
    }

    public function getData(): ?array
    {
        return $this->getObjectData();
    }

    public function hasProperty(string $property): bool
    {
        return array_key_exists($property, $this->getObjectData());
    }

    private function getObjectData(): array
    {
        return ['key1' => 7, 'key2' => 42];
    }
}
