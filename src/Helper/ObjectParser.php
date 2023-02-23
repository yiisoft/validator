<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Helper;

use Attribute;
use InvalidArgumentException;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\ExpectedValues;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionObject;
use ReflectionProperty;
use Yiisoft\Validator\AfterInitAttributeEventInterface;
use Yiisoft\Validator\AttributeTranslatorInterface;
use Yiisoft\Validator\AttributeTranslatorProviderInterface;
use Yiisoft\Validator\RuleInterface;

use function array_key_exists;
use function is_int;
use function is_object;
use function is_string;

/**
 * A helper class used to parse rules from PHP attributes (attached to class properties and class itself) and data from
 * object properties. The attributes introduced in PHP 8 simplify rules' configuration process, especially for nested
 * data and relations. This way the validated structures can be presented as DTO classes with references to each other.
 *
 * An example of parsed object with both one-to-one (requires PHP > 8.0) and one-to-many (requires PHP > 8.1) relations:
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
 * The parsed `$rules` will contain:
 *
 * ```php
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
 * A class name string is valid as a source too. This way only rules will be parsed:
 *
 * ```php
 * $parser = new ObjectParser(Post::class);
 * $rules = $parser->getRules(); // The result is the same as in previous example.
 * $data = $parser->getData(); // Returns empty array.
 * ```
 *
 * Please refer to the guide for more examples.
 *
 * Note that the rule attributes can be combined with others without affecting parsing. Which properties to parse can be
 * configured via {@see ObjectParser::$propertyVisibility} and {@see ObjectParser::$skipStaticProperties} options.
 *
 * Uses Reflection for getting object data and metadata. Supports caching for Reflection of a class / an obhect with
 * properties and rules which can be disabled on demand.
 *
 * @link https://www.php.net/manual/en/language.attributes.overview.php
 *
 * @psalm-type ObjectParserCache = array<string, array<string, mixed>>
 * @psalm-type RulesCacheItem = array{0:RuleInterface,1:Attribute::TARGET_*}
 */
final class ObjectParser
{
    /**
     * @var array A cache storage utilizing static class property:
     *
     * - The first nesting level is a mapping between cache keys (dynamically generated on instantiation) and item names
     * (one of: `rules`, `reflectionProperties`, `reflectionSource`).
     * - The second nesting level is a mapping between cache item names and their contents.
     *
     * Different properties' combinations of the same object are cached separately.
     * @psalm-var ObjectParserCache
     */
    #[ArrayShape([
        [
            'rules' => 'array',
            'reflectionAttributes' => 'array',
            'reflectionSource' => 'object',
        ],
    ])]
    private static array $cache = [];
    /**
     * @var string|null A cache key. Dynamically generated on instantiation.
     */
    private string|null $cacheKey = null;

    /**
     * @throws InvalidArgumentException If a class name string provided in {@see $source} refers to a non-existing
     * class.
     */
    public function __construct(
        /**
         * @var object|string A source for parsing rules and data. Can be either a class name string or an
         * instance.
         * @psalm-var class-string|object
         */
        private string|object $source,
        /**
         * @var int Visibility levels the parsed properties must have. For example: public and protected only, this
         * means that the rest (private ones) will be skipped. Defaults to all visibility levels (public, protected and
         * private).
         * @psalm-var int-mask-of<ReflectionProperty::IS_*>
         */
        private int $propertyVisibility = ReflectionProperty::IS_PRIVATE |
        ReflectionProperty::IS_PROTECTED |
        ReflectionProperty::IS_PUBLIC,
        /**
         * @var bool Whether the properties with "static" modifier must be skipped.
         */
        private bool $skipStaticProperties = false,
        /**
         * @var bool Whether some results of parsing (Reflection of a class / an object with properties and rules) must
         * be cached.
         */
        bool $useCache = true,
    ) {
        /** @var object|string $source */
        if (is_string($source) && !class_exists($source)) {
            throw new InvalidArgumentException(
                sprintf('Class "%s" not found.', $source)
            );
        }

        if ($useCache) {
            $this->cacheKey = (is_object($source) ? $source::class : $source)
                . '_' . $this->propertyVisibility
                . '_' . (int) $this->skipStaticProperties;
        }
    }

    /**
     * Parses rules specified via attributes attached to class properties and class itself. Repetitive calls utilize
     * cache if it's enabled in {@see $useCache}.
     *
     * @return array<int, RuleInterface>|array<string, list<RuleInterface>> The resulting rules array with the following
     * structure:
     *
     * ```php
     * [
     *     [new AtLeast(['name', 'author'])], // Parsed from class attribute.
     *     'files' => [new Count(max: 3)], // Parsed from property attribute.
     * ],
     * ```
     */
    public function getRules(): array
    {
        if ($this->hasCacheItem('rules')) {
            /** @var array $rules */
            $rules = $this->getCacheItem('rules');
            return $this->prepareRules($rules);
        }

        $rules = [];

        // Class rules
        $attributes = $this
            ->getReflectionSource()
            ->getAttributes(RuleInterface::class, ReflectionAttribute::IS_INSTANCEOF);
        foreach ($attributes as $attribute) {
            $rules[] = [$attribute->newInstance(), Attribute::TARGET_CLASS];
        }

        // Properties rules
        foreach ($this->getReflectionProperties() as $property) {
            // TODO: use Generator to collect attributes.
            $attributes = $property->getAttributes(RuleInterface::class, ReflectionAttribute::IS_INSTANCEOF);
            foreach ($attributes as $attribute) {
                /** @psalm-suppress UndefinedInterfaceMethod */
                $rules[$property->getName()][] = [$attribute->newInstance(), Attribute::TARGET_PROPERTY];
            }
        }

        $this->setCacheItem('rules', $rules);

        return $this->prepareRules($rules);
    }

    /**
     * Returns a property value of the parsed object.
     *
     * Note that in case of non-existing property a default `null` value is returned. If you need to check the presence
     * of a property or return a different default value, use {@see hasAttribute()} instead.
     *
     * If a {@see $source} is a class name string, `null` value is always returned.
     *
     * @param string $attribute Attribute name.
     *
     * @return mixed Attribute value.
     */
    public function getAttributeValue(string $attribute): mixed
    {
        return is_object($this->source)
            ? ($this->getReflectionProperties()[$attribute] ?? null)?->getValue($this->source)
            : null;
    }

    /**
     * Whether the parsed object has the property with a given name. Note that this means existence only and properties
     * with empty values are treated as present too.
     *
     * If a {@see $source} is a class name string, `false` value is always returned.
     *
     * @return bool Whether the property exists: `true` - exists and `false` - otherwise.
     */
    public function hasAttribute(string $attribute): bool
    {
        return is_object($this->source) && array_key_exists($attribute, $this->getReflectionProperties());
    }

    /**
     * Returns the parsed object's data as a whole in a form of associative array.
     *
     * If a {@see $source} is a class name string, an empty array is always returned.
     *
     * @return array A mapping between property names and their values.
     */
    public function getData(): array
    {
        if (!is_object($this->source)) {
            return [];
        }

        $data = [];
        foreach ($this->getReflectionProperties() as $name => $property) {
            /** @var mixed */
            $data[$name] = $property->getValue($this->source);
        }

        return $data;
    }

    /**
     * An optional attribute names translator. It's taken from the {@see $source} object when
     * {@see AttributeTranslatorProviderInterface} is implemented. In case of it's missing or {@see $source} being a
     * class name string, a `null` value is returned.
     *
     * @return AttributeTranslatorInterface|null An attribute translator instance or `null if it was not provided.
     */
    public function getAttributeTranslator(): ?AttributeTranslatorInterface
    {
        return $this->source instanceof AttributeTranslatorProviderInterface
            ? $this->source->getAttributeTranslator()
            : null;
    }

    /**
     * Returns Reflection properties parsed from {@see $source} in accordance with {@see $propertyVisibility} and
     * {@see $skipStaticProperties} values. Repetitive calls utilize cache if it's enabled in {@see $useCache}.
     *
     * @return array<string, ReflectionProperty> A mapping between Reflection property names and their values.
     *
     * @see https://github.com/yiisoft/form for usage in form collector.
     */
    public function getReflectionProperties(): array
    {
        if ($this->hasCacheItem('reflectionProperties')) {
            /** @var array<string, ReflectionProperty> */
            return $this->getCacheItem('reflectionProperties');
        }

        $reflectionProperties = [];
        foreach ($this->getReflectionSource()->getProperties($this->propertyVisibility) as $property) {
            if ($this->skipStaticProperties && $property->isStatic()) {
                continue;
            }

            /** @infection-ignore-all */
            if (PHP_VERSION_ID < 80100) {
                /** @psalm-suppress UnusedMethodCall Need for psalm with PHP 8.1+ */
                $property->setAccessible(true);
            }

            $reflectionProperties[$property->getName()] = $property;
        }

        $this->setCacheItem('reflectionProperties', $reflectionProperties);

        return $reflectionProperties;
    }

    /**
     * Gets cache storage.
     *
     * @return array Cache storage.
     * @psalm-return ObjectParserCache
     *
     * @see $cache
     *
     * @internal
     */
    public static function getCache(): array
    {
        return self::$cache;
    }

    /**
     * Returns Reflection of {@see $source}. Repetitive calls utilize cache if it's enabled in {@see $useCache}.
     *
     * @return ReflectionClass|ReflectionObject Either a Reflection class or an object instance depending on what was
     * provided in {@see $source}.
     */
    private function getReflectionSource(): ReflectionObject|ReflectionClass
    {
        if ($this->hasCacheItem('reflectionSource')) {
            /** @var ReflectionClass|ReflectionObject */
            return $this->getCacheItem('reflectionSource');
        }

        $reflectionSource = is_object($this->source)
            ? new ReflectionObject($this->source)
            : new ReflectionClass($this->source);

        $this->setCacheItem('reflectionSource', $reflectionSource);

        return $reflectionSource;
    }

    /**
     * @psalm-param array $source Raw rules containing additional metadata besides rule instances.
     *
     * @return array<int, RuleInterface>|array<string, list<RuleInterface>> An array of rules ready to use for the
     * validation.
     */
    private function prepareRules(array $source): array
    {
        $rules = [];
        /**
         * @var mixed $data
         */
        foreach ($source as $key => $data) {
            if (is_int($key)) {
                /** @psalm-var RulesCacheItem $data */
                $rules[$key] = $this->prepareRule($data[0], $data[1]);
            } else {
                /**
                 * @psalm-var list<RulesCacheItem> $data
                 * @psalm-suppress UndefinedInterfaceMethod
                 */
                foreach ($data as $rule) {
                    $rules[$key][] = $this->prepareRule($rule[0], $rule[1]);
                }
            }
        }
        return $rules;
    }

    /**
     * Prepares a rule instance created from a Reflection attribute to use for the validation.
     *
     * @param RuleInterface $rule A rule instance.
     * @param Attribute::TARGET_* $target {@see Attribute} target.
     *
     * @return RuleInterface The same rule instance.
     */
    private function prepareRule(RuleInterface $rule, int $target): RuleInterface
    {
        if (is_object($this->source) && $rule instanceof AfterInitAttributeEventInterface) {
            $rule->afterInitAttribute($this->source, $target);
        }
        return $rule;
    }

    /**
     * Whether a cache item with a given name exists in the cache. Note that this means existence only and items with
     * empty values are treated as present too.
     *
     * @param string $name Cache item name. Can be on of: `rules`, `reflectionProperties`, `reflectionSource`.
     *
     * @return bool `true` if an item exists, `false` - if it does not or the cache is disabled in {@see $useCache}.
     */
    private function hasCacheItem(
        #[ExpectedValues(['rules', 'reflectionProperties', 'reflectionSource'])]
        string $name,
    ): bool {
        if (!$this->useCache()) {
            return false;
        }

        if (!array_key_exists($this->cacheKey, self::$cache)) {
            return false;
        }

        return array_key_exists($name, self::$cache[$this->cacheKey]);
    }

    /**
     * Returns a cache item by its name.
     *
     * @param string $name Cache item name. Can be on of: `rules`, `reflectionProperties`, `reflectionSource`.
     *
     * @return mixed Cache item value.
     */
    private function getCacheItem(
        #[ExpectedValues(['rules', 'reflectionProperties', 'reflectionSource'])]
        string $name,
    ): mixed {
        /** @psalm-suppress PossiblyNullArrayOffset */
        return self::$cache[$this->cacheKey][$name];
    }

    /**
     * Updates cache item contents by its name.
     *
     * @param string $name Cache item name. Can be on of: `rules`, `reflectionProperties`, `reflectionSource`.
     * @param mixed $value A new value.
     */
    private function setCacheItem(
        #[ExpectedValues(['rules', 'reflectionProperties', 'reflectionSource'])]
        string $name,
        mixed $value,
    ): void {
        if (!$this->useCache()) {
            return;
        }

        /** @psalm-suppress PossiblyNullArrayOffset, MixedAssignment */
        self::$cache[$this->cacheKey][$name] = $value;
    }

    /**
     * Whether the cache is enabled / can be used for a particular object.
     *
     * @psalm-assert string $this->cacheKey
     *
     * @return bool `true` if the cache is enabled / can be used and `false` otherwise.
     */
    private function useCache(): bool
    {
        return $this->cacheKey !== null;
    }
}
