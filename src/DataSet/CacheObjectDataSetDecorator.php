<?php

declare(strict_types=1);

namespace Yiisoft\Validator\DataSet;

use JetBrains\PhpStorm\ArrayShape;
use Yiisoft\Validator\ObjectDataSetInterface;
use Yiisoft\Validator\RulesProviderInterface;

use function get_class;

final class CacheObjectDataSetDecorator implements ObjectDataSetInterface, RulesProviderInterface
{
    private string $cacheKey;

    #[ArrayShape([
        [
            'rules' => 'iterable',
            'propertyVisibility' => 'int',
        ],
    ])]
    private static array $cache = [];

    public function __construct(private ObjectDataSetInterface|RulesProviderInterface $decorated)
    {
        $this->cacheKey = get_class($this->decorated->getObject());
    }

    public function getAttributeValue(string $attribute): mixed
    {
        return $this->decorated->getAttributeValue($attribute);
    }

    public function getData(): mixed
    {
        return $this->decorated->getData();
    }

    public function hasAttribute(string $attribute): bool
    {
        return $this->decorated->hasAttribute($attribute);
    }

    public function getRules(): iterable
    {
        if (
            $this->hasCacheItem('rules') &&
            $this->hasCacheItem('propertyVisibility') &&
            $this->decorated->getPropertyVisibility() === $this->getCacheItem('propertyVisibility')
        ) {
            return $this->getCacheItem('rules');
        }

        $rules = $this->decorated->getRules();

        $this->updateCacheItem('rules', $rules);
        $this->updateCacheItem('propertyVisibility', $this->getPropertyVisibility());

        return $rules;
    }

    public function getPropertyVisibility(): int
    {
        return $this->decorated->getPropertyVisibility();
    }

    public function getObject(): object
    {
        return $this->decorated->getObject();
    }

    private function hasCacheItem(string $key): bool
    {
        return isset(self::$cache[$this->cacheKey][$key]);
    }

    #[ArrayShape([
        'rules' => 'iterable',
        'propertyVisibility' => 'int',
    ])]
    private function getCacheItem(string $key): mixed
    {
        return self::$cache[$this->cacheKey][$key];
    }

    private function updateCacheItem(string $key, mixed $value): void
    {
        self::$cache[$this->cacheKey][$key] = $value;
    }
}
