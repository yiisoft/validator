<?php

declare(strict_types=1);

namespace Yiisoft\Validator\DataSet;

use ReflectionAttribute;
use ReflectionObject;
use ReflectionProperty;
use Yiisoft\Validator\DataSetInterface;
use Yiisoft\Validator\RuleInterface;
use Yiisoft\Validator\RulesProviderInterface;

/**
 * This data set makes use of attributes introduced in PHP 8. It simplifies rules configuration process, especially for
 * nested data and relations. Please refer to the guide for example.
 *
 * @link https://www.php.net/manual/en/language.attributes.overview.php
 */
final class ObjectDataSet implements RulesProviderInterface, DataSetInterface
{
    private object $object;

    private bool $dataSetProvided;

    private int $propertyVisibility;

    /**
     * @var ReflectionProperty[]
     */
    private array $reflectionProperties = [];

    private iterable $rules;

    public function __construct(
        object $object,
        int $propertyVisibility = ReflectionProperty::IS_PRIVATE|ReflectionProperty::IS_PROTECTED|ReflectionProperty::IS_PUBLIC
    ) {
        $this->object = $object;
        $this->propertyVisibility = $propertyVisibility;
        $this->parseObject();
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

        return isset($this->reflectionProperties[$attribute])
            ? $this->reflectionProperties[$attribute]->getValue($this->object)
            : null;
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
        $objectProvidedRules = $this->object instanceof RulesProviderInterface;
        $this->dataSetProvided = $this->object instanceof DataSetInterface;

        $this->rules = $objectProvidedRules ? $this->object->getRules() : [];

        if ($this->dataSetProvided) {
            return;
        }

        $reflection = new ReflectionObject($this->object);
        foreach ($reflection->getProperties() as $property) {
            if (!$this->isUseProperty($property)) {
                continue;
            }

            if (PHP_VERSION_ID < 80100) {
                $property->setAccessible(true);
            }
            $this->reflectionProperties[$property->getName()] = $property;

            if (!$objectProvidedRules) {
                $attributes = $property->getAttributes(RuleInterface::class, ReflectionAttribute::IS_INSTANCEOF);
                foreach ($attributes as $attribute) {
                    $this->rules[$property->getName()][] = $attribute->newInstance();
                }
            }
        }
    }

    private function isUseProperty(ReflectionProperty $property): bool
    {
        return ($property->isPublic() && ($this->propertyVisibility&ReflectionProperty::IS_PUBLIC))
            || ($property->isPrivate() && ($this->propertyVisibility&ReflectionProperty::IS_PRIVATE))
            || ($property->isProtected() && ($this->propertyVisibility&ReflectionProperty::IS_PROTECTED));
    }
}
