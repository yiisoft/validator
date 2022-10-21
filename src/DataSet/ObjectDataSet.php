<?php

declare(strict_types=1);

namespace Yiisoft\Validator\DataSet;

use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\ExpectedValues;
use ReflectionAttribute;
use ReflectionObject;
use ReflectionProperty;
use Yiisoft\Validator\AttributeEventInterface;
use Yiisoft\Validator\DataSetInterface;
use Yiisoft\Validator\RuleInterface;
use Yiisoft\Validator\RulesProviderInterface;

use function array_key_exists;

/**
 * This data set makes use of attributes introduced in PHP 8. It simplifies rules configuration process, especially for
 * nested data and relations. Please refer to the guide for examples.
 *
 * @link https://www.php.net/manual/en/language.attributes.overview.php
 */
final class ObjectDataSet implements RulesProviderInterface, DataSetInterface
{
    private bool $dataSetProvided;
    private bool $rulesProvided;

    #[ArrayShape([
        [
            'rules' => 'iterable',
            'reflectionAttributes' => 'array',
        ],
    ])]
    private static array $cache = [];
    private string|null $cacheKey = null;

    public function __construct(
        private object $object,
        private int $propertyVisibility = ReflectionProperty::IS_PRIVATE |
        ReflectionProperty::IS_PROTECTED |
        ReflectionProperty::IS_PUBLIC,
        private bool $useCache = true
    ) {
        $this->dataSetProvided = $this->object instanceof DataSetInterface;
        $this->rulesProvided = $this->object instanceof RulesProviderInterface;

        if ($this->canCache()) {
            $this->cacheKey = $this->object::class . '_' . $this->propertyVisibility;
        }
    }

    public function getRules(): iterable
    {
        if ($this->rulesProvided) {
            return $this->object->getRules();
        }

        // Providing data set assumes object has its own attributes and rules getting logic. So further parsing of
        // Reflection properties and rules is skipped intentionally.
        if ($this->dataSetProvided) {
            return [];
        }

        if ($this->hasCacheItem('rules')) {
            return $this->getCacheItem('rules');
        }

        $rules = [];
        foreach ($this->getReflectionProperties() as $property) {
            // TODO: use Generator to collect attributes.
            $attributes = $property->getAttributes(RuleInterface::class, ReflectionAttribute::IS_INSTANCEOF);
            foreach ($attributes as $attribute) {
                $rule = $attribute->newInstance();
                $rules[$property->getName()][] = $rule;

                if ($rule instanceof AttributeEventInterface) {
                    $rule->afterInitAttribute($this);
                }
            }
        }

        if ($this->canCache()) {
            $this->setCacheItem('rules', $rules);
        }

        return $rules;
    }

    public function getObject(): object
    {
        return $this->object;
    }

    public function getAttributeValue(string $attribute): mixed
    {
        if ($this->dataSetProvided) {
            return $this->object->getAttributeValue($attribute);
        }

        return ($this->getReflectionProperties()[$attribute] ?? null)?->getValue($this->getObject());
    }

    public function hasAttribute(string $attribute): bool
    {
        return $this->dataSetProvided
            ? $this->object->hasAttribute($attribute)
            : array_key_exists($attribute, $this->getReflectionProperties());
    }

    public function getData(): array
    {
        if ($this->dataSetProvided) {
            return $this->object->getData();
        }

        $data = [];
        foreach ($this->getReflectionProperties() as $name => $property) {
            $data[$name] = $property->getValue($this->object);
        }

        return $data;
    }

    private function getReflectionProperties(): array
    {
        if ($this->hasCacheItem('reflectionProperties')) {
            return $this->getCacheItem('reflectionProperties');
        }

        $reflection = new ReflectionObject($this->object);
        $reflectionProperties = [];

        foreach ($reflection->getProperties($this->propertyVisibility) as $property) {
            if (PHP_VERSION_ID < 80100) {
                $property->setAccessible(true);
            }

            $reflectionProperties[$property->getName()] = $property;
        }

        if ($this->canCache()) {
            $this->setCacheItem('reflectionProperties', $reflectionProperties);
        }

        return $reflectionProperties;
    }

    private function canCache(): bool
    {
        return $this->useCache === true;
    }

    private function hasCacheItem(#[ExpectedValues(['rules', 'reflectionProperties'])] string $name): bool
    {
        if (!array_key_exists($this->cacheKey, self::$cache)) {
            return false;
        }

        return array_key_exists($name, self::$cache[$this->cacheKey]);
    }

    private function getCacheItem(#[ExpectedValues(['rules', 'reflectionProperties'])] string $name): array
    {
        return self::$cache[$this->cacheKey][$name];
    }

    private function setCacheItem(#[ExpectedValues(['rules', 'reflectionProperties'])] string $name, array $rules): void
    {
        self::$cache[$this->cacheKey][$name] = $rules;
    }
}
