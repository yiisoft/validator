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
     * @var ReflectionProperty[]
     */
    private ?array $reflectionProperties = null;
    private ?array $data = null;

    private static array $cachedRules = [];
    private string $cacheKey;

    public function __construct(
        private object $object,
        private int $propertyVisibility = ReflectionProperty::IS_PRIVATE |
        ReflectionProperty::IS_PROTECTED |
        ReflectionProperty::IS_PUBLIC
    ) {
        $this->dataSetProvided = $this->object instanceof DataSetInterface;
        $this->cacheKey = get_class($this->object) . '_' . $this->propertyVisibility;
    }

    public function getRules(): iterable
    {
        $objectHasRules = $this->object instanceof RulesProviderInterface;
        $rules = $objectHasRules ? $this->object->getRules() : [];

        // Providing data set assumes object has its own attributes and rules getting logic. So further parsing of
        // Reflection properties and rules is skipped intentionally.
        if ($this->dataSetProvided || $objectHasRules === true) {
            return $rules;
        }

        if ($this->hasCachedRules()) {
            return $this->getCachedRules();
        }

        foreach ($this->getReflectionProperties() as $property) {
            $attributes = $property->getAttributes(RuleInterface::class, ReflectionAttribute::IS_INSTANCEOF);
            foreach ($attributes as $attribute) {
                $rule = $attribute->newInstance();
                $rules[$property->getName()][] = $rule;

                if ($rule instanceof AttributeEventInterface) {
                    $rule->afterInitAttribute($this);
                }
            }
        }

        $this->setCachedRules($rules);

        return $rules;
    }

    /**
     * @return ReflectionProperty[] Used to avoid error "Typed property must not be accessed before initialization".
     */
    private function getReflectionProperties(): array
    {
        if ($this->reflectionProperties !== null) {
            return $this->reflectionProperties;
        }

        // Providing data set assumes object has its own attributes and rules getting logic. So further parsing of
        // Reflection properties and rules is skipped intentionally.
        if ($this->dataSetProvided) {
            $this->reflectionProperties = [];

            return $this->reflectionProperties;
        }

        // TODO: Use Generator to collect attributes

        $reflection = new ReflectionObject($this->object);
        $reflectionProperties = [];

        foreach ($reflection->getProperties($this->propertyVisibility) as $property) {
            if (PHP_VERSION_ID < 80100) {
                $property->setAccessible(true);
            }

            $reflectionProperties[$property->getName()] = $property;
        }

        $this->reflectionProperties = $reflectionProperties;

        return $reflectionProperties;
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

        return $this->getData()[$attribute] ?? null;
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

        if ($this->data !== null) {
            return $this->data;
        }

        $data = [];
        foreach ($this->getReflectionProperties() as $name => $property) {
            $data[$name] = $property->getValue($this->object);
        }

        $this->data = $data;

        return $data;
    }

    private function hasCachedRules(): bool
    {
        return array_key_exists($this->cacheKey, self::$cachedRules);
    }

    private function getCachedRules(): array
    {
        return self::$cachedRules[$this->cacheKey];
    }

    private function setCachedRules(array $rules): void
    {
        self::$cachedRules[$this->cacheKey] = $rules;
    }
}
