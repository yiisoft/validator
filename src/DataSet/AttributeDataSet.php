<?php

declare(strict_types=1);

namespace Yiisoft\Validator\DataSet;

use ReflectionAttribute;
use ReflectionObject;
use ReflectionProperty;
use Yiisoft\Validator\DataSetInterface;
use Yiisoft\Validator\Exception\MissingAttributeException;
use Yiisoft\Validator\RuleInterface;
use Yiisoft\Validator\RulesProviderInterface;

/**
 * This data set makes use of attributes introduced in PHP 8. It simplifies rules configuration process, especially for
 * nested data and relations. Please refer to the guide for example.
 *
 * @link https://www.php.net/manual/en/language.attributes.overview.php
 */
final class AttributeDataSet implements RulesProviderInterface, DataSetInterface
{
    private object $object;

    /**
     * @var ReflectionProperty[]
     */
    private array $reflectionProperties = [];

    private array $rules = [];

    public function __construct(object $object)
    {
        $this->object = $object;
        $this->collectRules();
    }

    public function getRules(): iterable
    {
        return $this->rules;
    }

    public function getAttributeValue(string $attribute)
    {
        if (!isset($this->reflectionProperties[$attribute])) {
            throw new MissingAttributeException("There is no \"$attribute\" key in the array.");
        }

        return $this->reflectionProperties[$attribute]->getValue($this->object);
    }

    public function getData(): array
    {
        $data = [];
        foreach ($this->reflectionProperties as $name => $property) {
            $data[$name] = $property->getValue($this->object);
        }

        return $data;
    }

    // TODO: use Generator to collect attributes
    private function collectRules(): void
    {
        $reflection = new ReflectionObject($this->object);
        foreach ($reflection->getProperties() as $property) {
            $attributes = $property->getAttributes(RuleInterface::class, ReflectionAttribute::IS_INSTANCEOF);
            if (!empty($attributes) || $property->isPublic()) {
                $this->reflectionProperties[$property->getName()] = $property;
            }
            foreach ($attributes as $attribute) {
                $this->rules[$property->getName()][] = $attribute->newInstance();
            }
        }
    }
}
