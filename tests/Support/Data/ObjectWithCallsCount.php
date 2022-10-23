<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Support\Data;

use Yiisoft\Validator\DataSetInterface;
use Yiisoft\Validator\Rule\HasLength;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\RulesProviderInterface;

use function array_key_exists;

final class ObjectWithCallsCount implements RulesProviderInterface, DataSetInterface
{
    public string $name = '';

    public static int $getRulesCallsCount = 0;
    public static int $getDataCallsCount = 0;

    public function getRules(): iterable
    {
        self::$getRulesCallsCount++;

        return [
            'name' => [new Required(), new HasLength(5)],
        ];
    }

    public function getData(): array
    {
        self::$getDataCallsCount++;

        return [
            'name' => 'foo',
        ];
    }

    public function getAttributeValue(string $attribute): mixed
    {
        return $this->getData()[$attribute] ?? null;
    }

    public function hasAttribute(string $attribute): bool
    {
        return array_key_exists($attribute, $this->getData());
    }
}
