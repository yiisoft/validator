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
 * nested data and relations. Please refer to the guide for example.
 *
 * @link https://www.php.net/manual/en/language.attributes.overview.php
 */
final class ObjectDataSet implements RulesProviderInterface, DataSetInterface
{
    private bool $dataSetProvided;

    /**
     * @var ReflectionProperty[]
     */
    private array $reflectionProperties = [];

    private iterable $rules;

    public function __construct(
        private object $object,
        private int $propertyVisibility = ReflectionProperty::IS_PRIVATE|ReflectionProperty::IS_PROTECTED|ReflectionProperty::IS_PUBLIC
    ) {
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

        return $this->hasAttribute($attribute)
            ? $this->reflectionProperties[$attribute]->getValue($this->object)
            : null;
    }

    public function hasAttribute(string $attribute): bool
    {
        return $this->dataSetProvided
            ? $this->object->hasAttribute($attribute)
            : array_key_exists($attribute, $this->reflectionProperties);
    }

    public function getData(): array
    {
        if ($this->dataSetProvided) {
            return $this->object->getData();
        }

        $data = [];
        foreach ($this->reflectionProperties as $name => $property) {
            $data[$name] = $property->getValue($this->object);
        }
        return $data;
    }

    // TODO: use Generator to collect attributes
    private function parseObject(): void
    {
        $objectHasRules = $this->object instanceof RulesProviderInterface;
        $this->rules = $objectHasRules ? $this->object->getRules() : [];

        $this->dataSetProvided = $this->object instanceof DataSetInterface;
        // Providing data set assumes object has its own attributes and rules getting logic. So further parsing of
        // Reflection properties and rules is intentionally skipped.
        if ($this->dataSetProvided) {
            return;
        }

        $reflection = new ReflectionObject($this->object);
        foreach ($reflection->getProperties($this->propertyVisibility) as $property) {
            if (PHP_VERSION_ID < 80100) {
                $property->setAccessible(true);
            }
            $this->reflectionProperties[$property->getName()] = $property;

            if ($objectHasRules === true) {
                continue;
            }

            $attributes = $property->getAttributes(RuleInterface::class, ReflectionAttribute::IS_INSTANCEOF);
            foreach ($attributes as $attribute) {
                $rule = $attribute->newInstance();
                $this->rules[$property->getName()][] = $rule;

                if ($rule instanceof AttributeEventInterface) {
                    $rule->afterInitAttribute($this);
                }
            }
        }
    }
}
