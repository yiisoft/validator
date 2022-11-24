<?php

declare(strict_types=1);

namespace Yiisoft\Validator\RulesProvider;

use ReflectionAttribute;
use ReflectionClass;
use ReflectionException;
use ReflectionObject;
use ReflectionProperty;
use Yiisoft\Validator\RuleInterface;
use Yiisoft\Validator\RulesProviderInterface;

use function is_object;

final class AttributesRulesProvider implements RulesProviderInterface
{
    private iterable|null $rules = null;

    public function __construct(
        /**
         * @var class-string|object
         */
        private string|object $source,
        private int $propertyVisibility = ReflectionProperty::IS_PRIVATE
        | ReflectionProperty::IS_PROTECTED
        | ReflectionProperty::IS_PUBLIC
    ) {
    }

    /**
     * @throws ReflectionException
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
     */
    private function parseRules(): iterable
    {
        $reflection = is_object($this->source)
            ? new ReflectionObject($this->source)
            : new ReflectionClass($this->source);

        $reflectionProperties = $reflection->getProperties($this->propertyVisibility);
        if ($reflectionProperties === []) {
            return [];
        }
        foreach ($reflectionProperties as $property) {
            $attributes = $property->getAttributes(RuleInterface::class, ReflectionAttribute::IS_INSTANCEOF);
            if ($attributes === []) {
                continue;
            }

            yield $property->getName() => $this->createAttributes($attributes);
        }
    }

    /**
     * @param array<array-key, ReflectionAttribute<RuleInterface>> $attributes
     */
    private function createAttributes(array $attributes): iterable
    {
        foreach ($attributes as $attribute) {
            yield $attribute->newInstance();
        }
    }
}
