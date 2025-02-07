<?php

declare(strict_types=1);

namespace Yiisoft\Validator\DataSet;

use ReflectionProperty;
use Traversable;
use Yiisoft\Validator\PropertyTranslatorInterface;
use Yiisoft\Validator\PropertyTranslatorProviderInterface;
use Yiisoft\Validator\DataSetInterface;
use Yiisoft\Validator\DataWrapperInterface;
use Yiisoft\Validator\Helper\ObjectParser;
use Yiisoft\Validator\LabelsProviderInterface;
use Yiisoft\Validator\RuleInterface;
use Yiisoft\Validator\RulesProvider\AttributesRulesProvider;
use Yiisoft\Validator\RulesProviderInterface;
use Yiisoft\Validator\ValidatorInterface;

use function array_unshift;
use function is_int;
use function is_iterable;

/**
 * A data set for object data. The object passed to this data set can provide rules and data by implementing
 * {@see RulesProviderInterface} and {@see DataSetInterface}. Alternatively this data set allows getting rules from PHP
 * attributes (attached to class properties and class itself) and data from object properties.
 *
 * An example of object implementing {@see RulesProviderInterface}:
 *
 * ```php
 * final class Author implements RulesProviderInterface
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
 * final class Author implements DataSetInterface
 * {
 *     public function getPropertyValue(string $property): mixed
 *     {
 *         return $this->getData()[$property] ?? null;
 *     }
 *
 *     public function getData(): mixed
 *     {
 *         return ['name' => 'John', 'age' => 18];
 *     }
 *
 *     public function hasProperty(string $property): bool
 *     {
 *         return array_key_exists($property, $this->getData());
 *     }
 * }
 * ```
 *
 * These two can be combined and used at the same time.
 *
 * The attributes introduced in PHP 8 simplify rules' configuration process, especially for nested data and relations.
 * This way the validated structures can be presented as DTO classes with references to each other.
 *
 * An example of DTO with both one-to-one (requires PHP > 8.0) and one-to-many (requires PHP > 8.1) relations:
 *
 * ```php
 * final class Post
 * {
 *     #[Length(max: 255)]
 *     public string $title = '';
 *
 *     #[Nested]
 *     public Author|null $author = null;
 *
 *     // Passing instances is available only since PHP 8.1.
 *     #[Each(new Nested(File::class))]
 *     public array $files = [];
 *
 *     public function __construct()
 *     {
 *         $this->author = new Author();
 *     }
 * }
 *
 * final class Author
 * {
 *     #[Length(min: 1)]
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
 * $post = new Post(title: 'Yii3 Overview 3', author: 'Dmitriy');
 * $parser = new ObjectParser($post);
 * $rules = $parser->getRules();
 * $data = $parser->getData();
 * ```
 *
 * The `$rules` will contain:
 *
 * ```
 * [
 *     new Nested([
 *         'title' => [new Length(max: 255)],
 *         'author' => new Nested([
 *             'name' => [new Length(min: 1)],
 *         ]),
 *         'files' => new Each([
 *             new Nested([
 *                 'url' => [new Url()],
 *             ]),
 *         ]),
 *     ]);
 * ];
 * ```
 *
 * And the result of `$data` will be:
 *
 * ```php
 * [
 *     'title' => 'Yii3 Overview 3',
 *     'author' => 'John',
 *     'files' => [],
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
 * Rules and data provided via separate methods have a higher priority over attributes and properties, so, when used
 * together, the latter ones will be ignored without exception.
 *
 * When {@see RulesProviderInterface} / {@see DataSetInterface} are not implemented, uses {@see ObjectParser} and
 * supports caching for data and attribute methods (partially) and rules (completely) which can be disabled on demand.
 *
 * For getting only rules by a class name string or to be able to skip static properties, use
 * {@see AttributesRulesProvider} instead.
 *
 * @link https://www.php.net/manual/en/language.attributes.overview.php
 *
 * @psalm-import-type RawRulesMap from ValidatorInterface
 */
final class ObjectDataSet implements
    RulesProviderInterface,
    DataWrapperInterface,
    LabelsProviderInterface,
    PropertyTranslatorProviderInterface
{
    /**
     * @var bool Whether an {@see $object} provided a data set by implementing {@see DataSetInterface}.
     */
    private readonly bool $dataSetProvided;
    /**
     * @var bool Whether an {@see $object} provided rules by implementing {@see RulesProviderInterface}.
     */
    private readonly bool $rulesProvided;
    /**
     * @var ObjectParser An object parser instance used to parse rules and data from attributes if these were not
     * provided by implementing {@see RulesProviderInterface} and {@see DataSetInterface} accordingly.
     */
    private readonly ObjectParser $parser;

    /**
     * @param int $propertyVisibility Visibility levels the properties with rules / data must have. For example: public
     * and protected only, this means that the rest (private ones) will be skipped. Defaults to all visibility levels
     * (public, protected and private).
     * @param bool $useCache Whether to use cache for data and attribute methods (partially) and rules (completely).
     *
     * @psalm-param int-mask-of<ReflectionProperty::IS_*> $propertyVisibility
     */
    public function __construct(
        /**
         * @var object An object containing rules and data.
         */
        private readonly object $object,
        int $propertyVisibility = ReflectionProperty::IS_PRIVATE |
        ReflectionProperty::IS_PROTECTED |
        ReflectionProperty::IS_PUBLIC,
        bool $useCache = true,
    ) {
        $this->dataSetProvided = $this->object instanceof DataSetInterface;
        $this->rulesProvided = $this->object instanceof RulesProviderInterface;
        $this->parser = new ObjectParser(
            source: $object,
            propertyVisibility: $propertyVisibility,
            useCache: $useCache
        );
    }

    /**
     * Returns {@see $object} rules specified via {@see RulesProviderInterface::getRules()} implementation or/and parsed
     * from attributes attached to class properties and class itself. For the latter case repetitive calls utilize cache
     * if it's enabled in {@see $useCache}. Rules provided via separate method have a lower priority over
     * PHP attributes, so, when used together, all rules will be merged, but rules from PHP attributes will be applied
     * first.
     *
     * @return iterable The resulting rules is an array with the following structure:
     *
     * @psalm-return RawRulesMap
     *
     * ```php
     * [
     *     [new FilledAtLeast(['name', 'author'])], // Rules not bound to a specific property.
     *     'files' => [new Count(max: 3)], // Property specific rules.
     * ],
     * ```
     */
    public function getRules(): iterable
    {
        if ($this->rulesProvided) {
            /** @var RulesProviderInterface $object */
            $object = $this->object;
            $rules = $object->getRules();
        } else {
            $rules = [];
        }

        // Providing data set assumes object has its own rules getting logic.
        // So further parsing of rules is skipped intentionally.
        if ($this->dataSetProvided) {
            return $rules;
        }

        // Merge rules from `RulesProviderInterface` implementation and parsed from PHP attributes.
        $rules = $rules instanceof Traversable ? iterator_to_array($rules) : $rules;
        foreach ($this->parser->getRules() as $key => $value) {
            if (is_int($key)) {
                array_unshift($rules, $value);
                continue;
            }

            /**
             * @psalm-var list<RuleInterface> $value If `$key` is string, then `$value` is array of rules
             * @see ObjectParser::getRules()
             */

            if (!isset($rules[$key])) {
                $rules[$key] = $value;
                continue;
            }

            $rules[$key] = is_iterable($rules[$key])
                ? [...$value, ...$rules[$key]]
                : [...$value, $rules[$key]];
        }

        return $rules;
    }

    /**
     * Returns a property value by its name.
     *
     * Note that in case of non-existing property a default `null` value is returned. If you need to check the presence
     * of property or return a different default value, use {@see hasProperty()} instead.
     *
     * @param string $property Property name.
     *
     * @return mixed Property value.
     */
    public function getPropertyValue(string $property): mixed
    {
        if ($this->dataSetProvided) {
            /** @var DataSetInterface $object */
            $object = $this->object;
            return $object->getPropertyValue($property);
        }

        return $this->parser->getPropertyValue($property);
    }

    /**
     * Whether this data set has the property with a given name. Note that this means existence only and properties
     * with empty values are treated as present too.
     *
     * @param string $property Property name.
     *
     * @return bool Whether the property exists: `true` - exists and `false` - otherwise.
     */
    public function hasProperty(string $property): bool
    {
        if ($this->dataSetProvided) {
            /** @var DataSetInterface $object */
            $object = $this->object;
            return $object->hasProperty($property);
        }

        return $this->parser->hasProperty($property);
    }

    /**
     * Returns the validated data as array.
     *
     * @return array|null Result of object {@see DataSetInterface::getData()} method, if it was implemented
     * {@see DataSetInterface}, otherwise returns the validated data as an associative array - a mapping between
     * property names and their values.
     */
    public function getData(): ?array
    {
        if ($this->dataSetProvided) {
            /** @var DataSetInterface $object */
            $object = $this->object;
            return $object->getData();
        }

        return $this->parser->getData();
    }

    public function getSource(): object
    {
        return $this->object;
    }

    /**
     * An optional property names translator. It's taken from the {@see $object} when
     * {@see PropertyTranslatorProviderInterface} is implemented. In case of it's missing, a `null` value is returned.
     *
     * @return PropertyTranslatorInterface|null A property translator instance or `null` if it was not provided.
     */
    public function getPropertyTranslator(): ?PropertyTranslatorInterface
    {
        return $this->parser->getPropertyTranslator();
    }

    public function getValidationPropertyLabels(): array
    {
        if ($this->object instanceof LabelsProviderInterface) {
            /** @var LabelsProviderInterface $object */
            $object = $this->object;
            return $object->getValidationPropertyLabels();
        }

        return $this->parser->getLabels();
    }
}
