<?php

declare(strict_types=1);

namespace Yiisoft\Validator\DataSet;

use ReflectionProperty;
use Yiisoft\Validator\DataSetInterface;
use Yiisoft\Validator\Helper\ObjectParser;
use Yiisoft\Validator\RuleInterface;
use Yiisoft\Validator\RulesProviderInterface;

/**
 * A data set for storing data as an object. The object passed to this data set can provide rules and data by
 * implementing {@see RuleInterface} and {@see DataSetInterface}. Alternatively this data set allows getting rules from
 * PHP attributes (attached to class properties and class itself) and data from object properties.
 *
 * An example of object implementing {@see RuleInterface}:
 *
 * ```php
 * final class ObjectWithRulesProvider implements RulesProviderInterface
 * {
 *     public function getRules(): iterable
 *     {
 *         return ['age' => [new Number(min: 18)]];
 *     }
 * }
 * ```
 *
 * An example of object implementing {@see DataSetInterface}:
 *
 * ```php
 * final class ObjectWithDataSet implements DataSetInterface
 * {
 *     public function getAttributeValue(string $attribute): mixed
 *     {
 *         return $this->getData()[$attribute] ?? null;
 *     }
 *
 *     public function getData(): mixed
 *     {
 *         return ['name' => 'John', 'age' => 18];
 *     }
 *
 *     public function hasAttribute(string $attribute): bool
 *     {
 *         return array_key_exists($attribute, $this->getData());
 *     }
 * }
 * ```
 *
 * These two can be combined and used at the same time.
 *
 * The attributes introduced in PHP 8 simplify rules' configuration process, especially for nested data and relations.
 * This way the validated structures can be presented as DTO classes with references to each other.
 *
 * An example of DTO with both one-to-one and one-to-many relations:
 *
 * ```php
 * final class Post
 * {
 *     #[HasLength(max: 255)]
 *     public string $title = '';
 *
 *     #[Nested]
 *     public Author $author;
 *
 *     #[Each(new Nested([File::class])]
 *     public array $files;
 *
 *     public function __construct()
 *     {
 *         $this->author = new Author();
 *     }
 * }
 *
 * final class Author
 * {
 *     #[HasLength(min: 1)]
 *     public string $name = '';
 * }
 *
 * // Some rules, like "Nested" can be also configured through the class attribute.
 *
 * #[Nested(['url' => new Url()])]
 * final class File
 * {
 *     public string $url = '';
 * }
 *
 * $post = new Post();
 * $parser = new ObjectParser($post);
 * $rules = $parser->getRules();
 * ```
 *
 * The `$rules` will contain:
 *
 * ```
 * $rules = [
 *     new Nested([
 *         'title' => [new HasLength(max: 255)],
 *         'author' => new Nested([
 *             'name' => [new HasLength(min: 1)],
 *         ]),
 *         'files' => new Each(new Nested([
 *             'url' => [new Url()],
 *         ])),
 *     ]);
 * ];
 * ```
 *
 * Note that the rule attributes can be combined with others without affecting parsing. Which properties to parse can be
 * configured via {@see ObjectDataSet::$propertyVisibility} and {@see ObjectDataSet::$skipStaticProperties} options.
 *
 * The other combinations of rules / data are also possible, for example: the data is provided by implementing
 * {@see DataSetInterface} and rules are parsed from the attributes.
 *
 * Please refer to the guide for more examples.
 *
 * Rules and data provided via separate methods have the highest priority over attributes and properties so, when using
 * together, the latter ones will be ignored without exception.
 *
 * When {@see RuleInterface} / {@see DataSetInterface} are not implemented, uses {@see ObjectParser} and supports
 * caching for data and attribute methods (partially) and rules (completely) which can be disabled on demand.
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
