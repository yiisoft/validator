<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Helper;

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
     * @psalm-var array<string, array<string, array>>
     */
    #[ArrayShape([
        [
            'rules' => 'array',
            'reflectionAttributes' => 'array',
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
            ? $this->object::class . '_' . $this->propertyVisibility
            : null;
    }

    /**
     * @return array<string, list<RuleInterface>>
     */
    public function getRules(): array
    {
        if ($this->hasCacheItem('rules')) {
            /** @var array<string, list<RuleInterface>> */
            return $this->getCacheItem('rules');
        }

        $rules = [];
        foreach ($this->getReflectionProperties() as $property) {
            // TODO: use Generator to collect attributes.
            $attributes = $property->getAttributes(RuleInterface::class, ReflectionAttribute::IS_INSTANCEOF);
            foreach ($attributes as $attribute) {
                $rule = $attribute->newInstance();
                $rules[$property->getName()][] = $rule;

                if ($rule instanceof AfterInitAttributeEventInterface) {
                    $rule->afterInitAttribute($this->object);
                }
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

        $reflection = new ReflectionObject($this->object);
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

    private function hasCacheItem(
        #[ExpectedValues(['rules', 'reflectionProperties'])]
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
        #[ExpectedValues(['rules', 'reflectionProperties'])]
        string $name
    ): array {
        /** @psalm-suppress PossiblyNullArrayOffset */
        return self::$cache[$this->cacheKey][$name];
    }

    private function setCacheItem(
        #[ExpectedValues(['rules', 'reflectionProperties'])]
        string $name,
        array $value
    ): void {
        /** @psalm-suppress PossiblyNullArrayOffset */
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
