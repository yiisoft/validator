<?php

declare(strict_types=1);

namespace Yiisoft\Validator\DataSet;

use ReflectionProperty;
use Yiisoft\Validator\DataSetInterface;
use Yiisoft\Validator\Helper\ObjectParser;
use Yiisoft\Validator\RulesProviderInterface;

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
    private ObjectParser $parser;

    public function __construct(
        private object $object,
        int $propertyVisibility = ReflectionProperty::IS_PRIVATE |
        ReflectionProperty::IS_PROTECTED |
        ReflectionProperty::IS_PUBLIC,
        bool $useCache = true
    ) {
        $this->dataSetProvided = $this->object instanceof DataSetInterface;
        $this->rulesProvided = $this->object instanceof RulesProviderInterface;
        $this->parser = new ObjectParser(
            object: $object,
            propertyVisibility: $propertyVisibility,
            useCache: $useCache
        );
    }

    public function getRules(): iterable
    {
        if ($this->rulesProvided) {
            /** @var RulesProviderInterface $object */
            $object = $this->object;

            return $object->getRules();
        }

        // Providing data set assumes object has its own attributes and rules getting logic. So further parsing of
        // Reflection properties and rules is skipped intentionally.
        if ($this->dataSetProvided) {
            return [];
        }

        return $this->parser->getRules();
    }

    public function getAttributeValue(string $attribute): mixed
    {
        if ($this->dataSetProvided) {
            /** @var DataSetInterface $object */
            $object = $this->object;
            return $object->getAttributeValue($attribute);
        }

        return $this->parser->getAttributeValue($attribute);
    }

    public function hasAttribute(string $attribute): bool
    {
        if ($this->dataSetProvided) {
            /** @var DataSetInterface $object */
            $object = $this->object;
            return $object->hasAttribute($attribute);
        }

        return $this->parser->hasAttribute($attribute);
    }

    public function getData(): mixed
    {
        if ($this->dataSetProvided) {
            /** @var DataSetInterface $object */
            $object = $this->object;
            return $object->getData();
        }

        return $this->parser->getData();
    }
}
