<?php

declare(strict_types=1);

namespace Yiisoft\Validator\DataSet;

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

    private ?array $cachedData = null;

    private static array $cache = [];
    private string $cacheKey;

    public function __construct(
        private object $object,
        private int $propertyVisibility = ReflectionProperty::IS_PRIVATE |
        ReflectionProperty::IS_PROTECTED |
        ReflectionProperty::IS_PUBLIC
    ) {
        $this->dataSetProvided = $this->object instanceof DataSetInterface;
        $this->rulesProvided = $this->object instanceof RulesProviderInterface;
        $this->cacheKey = $this->object::class . '_' . $this->propertyVisibility;
    }

    public function getObject(): object
    {
        return $this->object;
    }

    public function getRules(): iterable
    {
        if ($this->rulesProvided) {
            return $this->object->getRules();
        }

        if ($this->dataSetProvided) {
            return [];
        }

        return $this->getCachedRules();
    }

    public function getAttributeValue(string $attribute): mixed
    {
        if ($this->dataSetProvided) {
            return $this->object->getAttributeValue($attribute);
        }

        return $this->getData()[$attribute] ?? null;
    }

    public function hasAttribute(string $attribute): bool
    {
        return $this->dataSetProvided
            ? $this->object->hasAttribute($attribute)
            : array_key_exists($attribute, $this->getCachedReflectionProperties());
    }

    public function getData(): array
    {
        if ($this->dataSetProvided) {
            return $this->object->getData();
        }

        if ($this->cachedData !== null) {
            return $this->cachedData;
        }

        $this->cachedData = [];
        foreach ($this->getCachedReflectionProperties() as $name => $property) {
            $this->cachedData[$name] = $property->getValue($this->object);
        }

        return $this->cachedData;
    }

    private function getCachedRules(): array
    {
        $this->assertRulesAndReflectionPropertiesInCache();
        return self::$cache[$this->cacheKey]['rules'];
    }

    private function getCachedReflectionProperties(): array
    {
        $this->assertRulesAndReflectionPropertiesInCache();
        return self::$cache[$this->cacheKey]['reflectionProperties'];
    }

    // TODO: use Generator to collect attributes
    private function assertRulesAndReflectionPropertiesInCache(): void
    {
        if (array_key_exists($this->cacheKey, self::$cache)) {
            return;
        }

        $rules = [];
        $reflectionProperties = [];

        $reflection = new ReflectionObject($this->object);
        foreach ($reflection->getProperties($this->propertyVisibility) as $property) {
            if (PHP_VERSION_ID < 80100) {
                $property->setAccessible(true);
            }
            $reflectionProperties[$property->getName()] = $property;

            if ($this->rulesProvided) {
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

        self::$cache[$this->cacheKey] = [
            'rules' => $rules,
            'reflectionProperties' => $reflectionProperties,
        ];
    }
}
