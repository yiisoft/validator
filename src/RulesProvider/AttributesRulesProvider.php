<?php

declare(strict_types=1);

namespace Yiisoft\Validator\RulesProvider;

use Generator;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionException;
use ReflectionObject;
use ReflectionProperty;
use Yiisoft\Validator\RuleInterface;
use Yiisoft\Validator\RulesProviderInterface;

final class AttributesRulesProvider implements RulesProviderInterface
{
    private Generator|iterable|null $rules = null;

    public function __construct(
        /**
         * @param class-string|object $class
         */
        private string|object $source,
        private int $propertyVisibility = ReflectionProperty::IS_PRIVATE | ReflectionProperty::IS_PROTECTED | ReflectionProperty::IS_PUBLIC
    ) {
    }

    /**
     * @throws ReflectionException
     *
     * @return iterable
     */
    public function getRules(): iterable
    {
        if ($this->rules === null) {
            $this->rules = $this->parseRules();
        }

        yield from $this->rules;
    }

    /**
     * @throws ReflectionException
     *
     * @return Generator
     */
    private function parseRules(): iterable
    {
        $reflection = is_object($this->source)
            ? new ReflectionObject($this->source)
            : new ReflectionClass($this->source);

        $reflectionProperties = $reflection->getProperties();
        if ($reflectionProperties === []) {
            return [];
        }
        foreach ($reflectionProperties as $property) {
            if (!$this->isUseProperty($property)) {
                continue;
            }

            $attributes = $property->getAttributes(RuleInterface::class, ReflectionAttribute::IS_INSTANCEOF);
            if ($attributes === []) {
                continue;
            }

            yield $property->getName() => $this->createAttributes($attributes);
        }
    }

    /**
     * @param array<array-key, ReflectionAttribute<RuleInterface>> $attributes
     *
     * @return iterable
     */
    private function createAttributes(array $attributes): iterable
    {
        foreach ($attributes as $attribute) {
            yield $attribute->newInstance();
        }
    }

    private function isUseProperty(ReflectionProperty $property): bool
    {
        return ($property->isPublic() && ($this->propertyVisibility & ReflectionProperty::IS_PUBLIC))
            || ($property->isPrivate() && ($this->propertyVisibility & ReflectionProperty::IS_PRIVATE))
            || ($property->isProtected() && ($this->propertyVisibility & ReflectionProperty::IS_PROTECTED));
    }
}
