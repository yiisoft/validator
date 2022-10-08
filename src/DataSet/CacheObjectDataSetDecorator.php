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
            'reflectionProperties' => 'array',
            'data' => 'array',
        ],
    ])]
    private static array $cache = [];

    public function __construct(private ObjectDataSetInterface|RulesProviderInterface $decorated) {
        $this->cacheKey = get_class($this->decorated->getObject());

        $this->deleteCacheItem('data');
    }

    public function getAttributeValue(string $attribute): mixed
    {
        return $this->decorated->getAttributeValue($attribute);
    }

    public function getData(): mixed
    {
        if ($this->hasCacheItem('data')) {
            return $this->getCacheItem('data');
        }

        $data = $this->decorated->getData();
        $this->updateCacheItem('data', $data);

        return $data;
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

    public function getReflectionProperties(): array
    {
        if (
            $this->hasCacheItem('reflectionProperties') &&
            $this->hasCacheItem('propertyVisibility') &&
            $this->decorated->getPropertyVisibility() === $this->getCacheItem('propertyVisibility')
        ) {
            return $this->getCacheItem('reflectionProperties');
        }

        $reflectionProperties = $this->decorated->getReflectionProperties();

        $this->updateCacheItem('reflectionProperties', $reflectionProperties);
        $this->updateCacheItem('propertyVisibility', $this->getPropertyVisibility());

        return $reflectionProperties;
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
        'reflectionProperties' => 'array',
        'data' => 'array',
    ])]
    private function getCacheItem(string $key): mixed
    {
        return self::$cache[$this->cacheKey][$key];
    }

    private function updateCacheItem(string $key, mixed $value): void
    {
        self::$cache[$this->cacheKey][$key] = $value;
    }

    private function deleteCacheItem(string $key): void
    {
        unset(self::$cache[$this->cacheKey][$key]);
    }
}
