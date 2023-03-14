<?php

declare(strict_types=1);

namespace Yiisoft\Validator\RulesProvider;

use ReflectionProperty;
use Yiisoft\Validator\DataSet\ObjectDataSet;
use Yiisoft\Validator\Helper\ObjectParser;
use Yiisoft\Validator\RulesProviderInterface;
use Yiisoft\Validator\ValidatorInterface;

/**
 * A rules provider that extracts rules from PHP attributes (attached to class properties and class itself). The
 * attributes introduced in PHP 8 simplify rules' configuration process, especially for nested data and relations. This
 * way the validated structures can be presented as DTO classes with references to each other.
 *
 * An example of object with both one-to-one (requires PHP > 8.0) and one-to-many (requires PHP > 8.1) relations:
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
 * ```
 *
 * The `$rules` will contain:
 *
 * ```php
 * [
 *     new Nested([
 *         'title' => [new Length(max: 255)],
 *         'author' => new Nested([
 *             'name' => [new Length(min: 1)],
 *         ]),
 *         'files' => new Each(new Nested([
 *             'url' => [new Url()],
 *         ])),
 *     ]);
 * ];
 * ```
 *
 * A class name string is valid as a source too:
 *
 * ```php
 * $parser = new ObjectParser(Post::class);
 * $rules = $parser->getRules(); // The result is the same as in previous example.
 * ```
 *
 * Please refer to the guide for more examples.
 *
 * Note that the rule attributes can be combined with others without affecting parsing. Which properties to parse can be
 * configured via `$propertyVisibility` and `$skipStaticProperties` constructor arguments.
 *
 * Uses {@see ObjectParser} and supports caching.
 *
 * If you want to additionally extract data or to be able to disable cache, use {@see ObjectDataSet} or
 * {@see ObjectParser} directly instead.
 *
 * @link https://www.php.net/manual/en/language.attributes.overview.php
 *
 * @psalm-import-type RawRulesMap from ValidatorInterface
 */
final class AttributesRulesProvider implements RulesProviderInterface
{
    /**
     * @var ObjectParser An object parser instance used to parse rules from attributes.
     */
    private ObjectParser $parser;

    /**
     * @param class-string|object $source A source for parsing rules. Can be either a class name string or an instance.
     * @param int $propertyVisibility Visibility levels the properties with rules must have. For example: public and
     * protected only, this means that the rest (private ones) will be skipped. Defaults to all visibility levels
     * (public, protected and private).
     * @param bool $skipStaticProperties Whether the properties with "static" modifier must be skipped.
     *
     * @psalm-param int-mask-of<ReflectionProperty::IS_*> $propertyVisibility
     */
    public function __construct(
        string|object $source,
        int $propertyVisibility = ReflectionProperty::IS_PRIVATE
        | ReflectionProperty::IS_PROTECTED
        | ReflectionProperty::IS_PUBLIC,
        bool $skipStaticProperties = false,
    ) {
        $this->parser = new ObjectParser($source, $propertyVisibility, $skipStaticProperties);
    }

    /**
     * Returns rules parsed from attributes attached to class properties and class itself. Repetitive calls utilize
     * cache.
     *
     * @return iterable The resulting rules is an array with the following structure:
     *
     * ```php
     * [
     *     [new AtLeast(['name', 'author'])], // Rules not bound to a specific attribute.
     *     'files' => [new Count(max: 3)], // Attribute specific rules.
     * ],
     * ```
     *
     * @psalm-return RawRulesMap
     */
    public function getRules(): iterable
    {
        return $this->parser->getRules();
    }
}
