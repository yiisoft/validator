<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Helper;

use Attribute;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\ExpectedValues;
use ReflectionAttribute;
use ReflectionObject;
use ReflectionProperty;
use Yiisoft\Validator\AfterInitAttributeEventInterface;
use Yiisoft\Validator\RuleInterface;

use function array_key_exists;

final class ObjectParser
{
    /**
     * @var array<string, array<string, mixed>>
     */
    #[ArrayShape([
        [
            'rules' => 'array',
            'reflectionAttributes' => 'array',
            'reflectionObject' => 'object',
        ],
    ])]
    private static array $cache = [];
    private string|null $cacheKey;

    public function __construct(
        private object $object,
        private int $propertyVisibility = ReflectionProperty::IS_PRIVATE |
        ReflectionProperty::IS_PROTECTED |
        ReflectionProperty::IS_PUBLIC,
        private bool $skipStaticProperties = false,
        bool $useCache = true
    ) {
        $this->cacheKey = $useCache
            ? $this->object::class . '_' . $this->propertyVisibility . '_' . $this->skipStaticProperties
            : null;
    }

    /**
     * @return array<int, RuleInterface>|array<string, list<RuleInterface>>
     */
    public function getRules(): array
    {
        if ($this->hasCacheItem('rules')) {
            /** @var array<int, RuleInterface>|array<string, list<RuleInterface>> */
            return $this->getCacheItem('rules');
        }

        $rules = [];

        // Class rules
        $attributes = $this
            ->getReflectionObject()
            ->getAttributes(RuleInterface::class, ReflectionAttribute::IS_INSTANCEOF);
        foreach ($attributes as $attribute) {
            $rules[] = $this->createRule($attribute, Attribute::TARGET_CLASS);
        }

        // Properties rules
        foreach ($this->getReflectionProperties() as $property) {
            // TODO: use Generator to collect attributes.
            $attributes = $property->getAttributes(RuleInterface::class, ReflectionAttribute::IS_INSTANCEOF);
            foreach ($attributes as $attribute) {
                /** @psalm-suppress UndefinedInterfaceMethod */
                $rules[$property->getName()][] = $this->createRule($attribute, Attribute::TARGET_PROPERTY);
            }
        }

        if ($this->useCache()) {
            $this->setCacheItem('rules', $rules);
        }

        return $rules;
    }

    public function getAttributeValue(string $attribute): mixed
    {
        return ($this->getReflectionProperties()[$attribute] ?? null)?->getValue($this->object);
    }

    public function hasAttribute(string $attribute): bool
    {
        return array_key_exists($attribute, $this->getReflectionProperties());
    }

    public function getData(): array
    {
        $data = [];
        foreach ($this->getReflectionProperties() as $name => $property) {
            /** @var mixed */
            $data[$name] = $property->getValue($this->object);
        }

        return $data;
    }

    /**
     * @return array<string, ReflectionProperty>
     */
    public function getReflectionProperties(): array
    {
        if ($this->hasCacheItem('reflectionProperties')) {
            /** @var array<string, ReflectionProperty> */
            return $this->getCacheItem('reflectionProperties');
        }

        $reflection = $this->getReflectionObject();

        $reflectionProperties = [];

        foreach ($reflection->getProperties($this->propertyVisibility) as $property) {
            if ($this->skipStaticProperties && $property->isStatic()) {
                continue;
            }

            if (PHP_VERSION_ID < 80100) {
                $property->setAccessible(true);
            }

            $reflectionProperties[$property->getName()] = $property;
        }

        if ($this->useCache()) {
            $this->setCacheItem('reflectionProperties', $reflectionProperties);
        }

        return $reflectionProperties;
    }

    private function getReflectionObject(): ReflectionObject
    {
        if ($this->hasCacheItem('reflectionObject')) {
            /** @var ReflectionObject */
            return $this->getCacheItem('reflectionObject');
        }

        $reflection = new ReflectionObject($this->object);

        if ($this->useCache()) {
            $this->setCacheItem('reflectionObject', $reflection);
        }

        return $reflection;
    }

    /**
     * @param ReflectionAttribute<RuleInterface> $attribute
     */
    private function createRule(ReflectionAttribute $attribute, int $target): RuleInterface
    {
        $rule = $attribute->newInstance();

        if ($rule instanceof AfterInitAttributeEventInterface) {
            $rule->afterInitAttribute($this->object, $target);
        }

        return $rule;
    }

    private function hasCacheItem(
        #[ExpectedValues(['rules', 'reflectionProperties', 'reflectionObject'])]
        string $name
    ): bool {
        if (!$this->useCache()) {
            return false;
        }

        if (!array_key_exists($this->cacheKey, self::$cache)) {
            return false;
        }

        return array_key_exists($name, self::$cache[$this->cacheKey]);
    }

    private function getCacheItem(
        #[ExpectedValues(['rules', 'reflectionProperties', 'reflectionObject'])]
        string $name
    ): mixed {
        /** @psalm-suppress PossiblyNullArrayOffset */
        return self::$cache[$this->cacheKey][$name];
    }

    private function setCacheItem(
        #[ExpectedValues(['rules', 'reflectionProperties', 'reflectionObject'])]
        string $name,
        mixed $value
    ): void {
        /** @psalm-suppress PossiblyNullArrayOffset, MixedAssignment */
        self::$cache[$this->cacheKey][$name] = $value;
    }

    /**
     * @psalm-assert string $this->cacheKey
     */
    private function useCache(): bool
    {
        return $this->cacheKey !== null;
    }
}
