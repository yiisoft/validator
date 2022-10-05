<?php

declare(strict_types=1);

namespace Yiisoft\Validator\DataSet;

use JetBrains\PhpStorm\ArrayShape;
use ReflectionAttribute;
use ReflectionObject;
use ReflectionProperty;
use Yiisoft\Validator\AttributeEventInterface;
use Yiisoft\Validator\DataSetInterface;
use Yiisoft\Validator\RuleInterface;
use Yiisoft\Validator\RulesProviderInterface;

use function get_class;

/**
 * This data set makes use of attributes introduced in PHP 8. It simplifies rules configuration process, especially for
 * nested data and relations. Please refer to the guide for examples.
 *
 * @link https://www.php.net/manual/en/language.attributes.overview.php
 */
final class ObjectDataSet implements RulesProviderInterface, DataSetInterface
{
    private bool $dataSetProvided;

    /**
     * @var ReflectionProperty[] Used to avoid error "Typed property must not be accessed before initialization".
     */
    private array $reflectionProperties = [];
    private string $cacheKey;

    #[ArrayShape([
        [
            'rules' => 'iterable',
            'reflectionProperties' => 'array',
            'data' => 'array',
        ],
    ])]
    private static array $cache;

    private iterable $rules;

    public function __construct(
        private object $object,
        private int $propertyVisibility = ReflectionProperty::IS_PRIVATE |
        ReflectionProperty::IS_PROTECTED |
        ReflectionProperty::IS_PUBLIC,
    ) {
        $this->cacheKey = get_class($this->object);
        $this->parseObject();
    }

    public function getObject(): object
    {
        return $this->object;
    }

    public function getRules(): iterable
    {
        return $this->rules;
    }

    public function getAttributeValue(string $attribute): mixed
    {
        if ($this->dataSetProvided) {
            return $this->object->getAttributeValue($attribute);
        }

        return $this->getData()[$attribute] ?? null;
    }

    public function getData(): array
    {
        $cacheItem = $this->getCacheItem();
        if (isset($cacheItem['data'])) {
            return $cacheItem['data'];
        }

        if ($this->dataSetProvided) {
            $data = $this->object->getData();
        } else {
            $data = [];
            foreach ($this->reflectionProperties as $name => $property) {
                $data[$name] = $property->getValue($this->object);
            }
        }

        $this->updateCacheItem('data', $data);

        return $data;
    }

    // TODO: Use Generator to collect attributes
    private function parseObject(): void
    {
        $this->dataSetProvided = $this->object instanceof DataSetInterface;

        if (
            $this->hasCacheItem() &&
            $this->propertyVisibility === $this->getCacheItem()['propertyVisibility']
        ) {
            $this->rules = $this->getCacheItem()['rules'];
            $this->reflectionProperties = $this->getCacheItem()['reflectionProperties'] ?? [];

            return;
        }

        $this->rules = $this->parseRules();

        $this->updateCacheItem('rules', $this->rules);
        $this->updateCacheItem('propertyVisibility', $this->propertyVisibility);
        $this->deleteCacheItem('data');
    }

    private function parseRules(): iterable
    {
        $objectHasRules = $this->object instanceof RulesProviderInterface;
        $rules = $objectHasRules ? $this->object->getRules() : [];

        if ($this->dataSetProvided) {
            return $rules;
        }

        $reflection = new ReflectionObject($this->object);
        $reflectionProperties = [];

        foreach ($reflection->getProperties($this->propertyVisibility) as $property) {
            if (PHP_VERSION_ID < 80100) {
                $property->setAccessible(true);
            }

            $reflectionProperties[$property->getName()] = $property;

            if ($objectHasRules === true) {
                continue;
            }

            $attributes = $property->getAttributes(RuleInterface::class, ReflectionAttribute::IS_INSTANCEOF);
            foreach ($attributes as $attribute) {
                $rule = $attribute->newInstance();
                $rules[$property->getName()][] = $rule;

                if ($rule instanceof AttributeEventInterface) {
                    $rule->afterInitAttribute($this);
                }
            }
        }

        $this->reflectionProperties = $reflectionProperties;
        $this->updateCacheItem('reflectionProperties', $this->reflectionProperties);

        return $rules;
    }

    private function hasCacheItem(): bool
    {
        return isset(self::$cache[$this->cacheKey]);
    }

    #[ArrayShape([
        'rules' => 'iterable',
        'reflectionProperties' => 'array',
        'data' => 'array',
    ])]
    private function getCacheItem(): array
    {
        return self::$cache[$this->cacheKey];
    }

    private function updateCacheItem($key, $value): void
    {
        self::$cache[$this->cacheKey][$key] = $value;
    }

    private function deleteCacheItem(string $key): void
    {
        unset(self::$cache[$this->cacheKey][$key]);
    }
}
