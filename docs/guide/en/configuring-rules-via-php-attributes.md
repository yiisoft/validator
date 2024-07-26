# Configuring rules via PHP attributes

The [Attributes] feature introduced in PHP 8 allows an alternative way of configuring rules. If entities/models with
their relations are represented as [DTO] classes, attributes make it possible to use such classes to provide rules.
The rules are defined above the properties themselves, which some developers may find more convenient in terms
of readability.

## Configuring for a single entity / model

Given a single `User` entity / model:

```php
use Yiisoft\Validator\Rule\Integer;
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\Rule\Required;

[
    'name' => [
        new Required(),
        new Length(min: 1, max: 50),
    ],
    'age' => [
        new Integer(min: 18, max: 100),
    ],
]
```

the PHP attributes equivalent will be:

```php
use JetBrains\PhpStorm\Deprecated;
use Yiisoft\Validator\Rule\Integer;
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\Rule\Required;

final class User
{
    public function __construct(
        // Multiple attributes.
        #[Required]
        #[Length(min: 1, max: 50)]
        // Can be combined with other attributes not related with rules.
        #[Deprecated]
        private readonly string $name,
        // Single attribute.
        #[Integer(min: 18, max: 100)]
        private readonly int $age,
    ) {
    }
}
```

This example uses the [constructor property promotion] feature introduced in PHP 8. Attributes can also
be used with regular properties:

```php
use Yiisoft\Validator\Rule\Integer;
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\Rule\Required;

final class User
{
    // Multiple attributes.
    #[Required]
    #[Length(min: 1, max: 50)]
    public readonly string $name;

    // Single attribute.
    #[Integer(min: 18, max: 100)]
    public readonly int $age;
}
```

> **Note:** [readonly properties] are supported only starting from PHP 8.1.

Error messages may include `{property}` placeholder that is replaced with the name of the property. To capitalize the 
first letter, you can use the `{Property}` placeholder. If you would like the name to be replaced with a custom value, 
you can specify it using the `Label` attribute:

```php
use Yiisoft\Validator\Label;
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\Rule\Required;

final class User
{
    #[Required]
    #[Length(min: 1, max: 50)]
    #[Label('First Name')]
    public readonly string $name;
}
```

> **Note:** [readonly properties] are supported only starting from PHP 8.1.

## Configuring for multiple entities / models with relations

An example of rule set for a blog post configured via arrays only:

```php
use Yiisoft\Validator\Rule\Each;
use Yiisoft\Validator\Rule\Integer;
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\Rule\Nested;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\Rule\Url;

[
    new Nested([
        'title' => [
            new Length(min:1, max: 255),
        ],
        // One-to-one relation.
        'author' => new Nested([
            'name' => [
                new Required(),
                new Length(min: 1, max: 50),
            ],
            'age' => [
                new Integer(min: 18, max: 100),
            ],
        ]),
        // One-to-many relation.
        'files' => new Each([
            new Nested([
                'url' => [new Url()],
            ]),
        ]),
    ]),
];
```

It can be applied to such DTO classes to achieve the same effect:

```php
use Yiisoft\Validator\Rule\Each;
use Yiisoft\Validator\Rule\Integer;
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\Rule\Nested;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\Rule\Url;

final class Post
{
    #[Length(min: 1, max: 255)]
    public string $title;

    // "Nested" can be used without arguments, but make sure to fill the value with the instance in this case (here it's
    // filled right in the constructor).
    #[Nested]
    public Author|null $author = null;

    // Passing instances is available only since PHP 8.1.
    #[Each(new Nested(File::class))]
    public array $files = [];

    public function __construct()
    {
        $this->author = new Author();
    }
}

final class Author
{
    #[Required]
    #[Length(min: 1, max: 50)]
    public string $name;

    #[Integer(min: 18, max: 100)]
    public int $age;
}

// Some rules, like "Nested" can be also configured through the class attribute.

#[Nested(['url' => new Url()])]
final class File
{
    public string $url;
}
```

For a better understanding of relations concept, it's recommended to read the [Nested] and [Each] guides.

## Traits

Attributes can also be used in traits. It might come in handy for reusing the same set of properties with identical 
rules:

```php
use Yiisoft\Validator\Rule\Length;

trait TitleTrait
{
    #[Length(max: 255)]
    public string $title;
}

final class BlogPost
{
    use TitleTrait;
}

final class WikiArticle
{
    use TitleTrait;
}
```

## Inheritance

Inheritance is supported, but there are some things to keep in mind:

```php
use Yiisoft\Validator\Rule\BooleanValue;
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Rule\Required;

class Car
{
    #[Required]
    #[Length(min: 1, max: 50)]
    public string $name;
    
    #[Required]
    #[BooleanValue]
    public $used;
    
    #[Required]
    #[Number(max: 2000)]
    public float $weight;     
}

class Truck extends Car
{       
    public string $name;
    
    #[Number(max: 3500)]
    public float $weight;      
}
```

In this case the set of rules for `Truck` class will be:

```php
use Yiisoft\Validator\Rule\BooleanValue;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Rule\Required;

[
    'used' => [
        new Required(),
        new BooleanValue(),
    ],
    'weight' => [
        new Number(max: 3500),
    ],
];
```

So, to sum up:

- Parent rules for overridden properties are ignored completely, only the ones from the child class are obtained.
- All parent rules for properties that are not overridden in the child class are obtained fully.

As for the data, default values set in the child class take precedence.

## Adding attributes support to custom rules

In order to attach rules to DTO properties or the whole DTO, the `Attribute` attribute must be added to the 
custom class. And in order for rules to be fetched from attributes, they must implement the `RuleInterface`.

For custom `Composite` rules you only need to add the attribute:

```php
use Attribute;
use Yiisoft\Validator\Rule\Composite;
use Yiisoft\Validator\Rule\Count;
use Yiisoft\Validator\Rule\Each;
use Yiisoft\Validator\Rule\Integer;

// Make sure to add this because attribute inheritance is not supported.
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
final class RgbColorRuleSet extends Composite
{
    public function getRules(): array
    {
        return [
            new Count(3),
            new Each([new Integer(min: 0, max: 255)])
        ];
    }
}
```

Example for custom rule:

```php
use Attribute;
use Yiisoft\Validator\RuleInterface;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
final class Yaml implements RuleInterface
{
    public function __construct(
        public string $incorrectInputMessage = 'Value must be a string. {type} given.',
        public string $message = 'The value is not a valid YAML.',
    ) {
    }

    public function getHandler(): string
    {
        return YamlHandler::class;
    }
}
```

To allow attaching to the class, modify the attribute definition like this:

```php
use Attribute;
use Yiisoft\Validator\RuleInterface;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
final class Yaml implements RuleInterface 
{
    // ...
}
```

## Limitations and workarounds

### Instances

Passing instances in the scope of attributes is only possible as of PHP 8.1. This means using attributes for complex
rules such as `Composite`, `Each` and `Nested` or rules that take instances as arguments, can be problematic with PHP 8.0.

The first workaround is to upgrade to PHP 8.1 - this is fairly simple since it is a minor version. Tools like 
[Rector] can ease the process of upgrading the code base by automating routine tasks.

If this is not an option, you can use other ways of providing rules, such as rule providers:

```php
use Yiisoft\Validator\Rule\Integer;
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\Rule\Nested;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\Rule\Url;
use Yiisoft\Validator\RulesProviderInterface;
use Yiisoft\Validator\Validator;

final class Post
{
    public function __construct(
        private string $title,
        private Author|null $author = null,
        private array $files = [],
    ) {
    }
}

final class Author
{
    public function __construct(
        private string $name,
        private int $age,
    ) {
    }
}

final class File
{
    private string $url;
}

final class PostRulesProvider implements RulesProviderInterface
{
    public function getRules(): array
    {
        return [
            new Nested([
                'title' => new Length(min:1, max: 255),
                'author' => [
                    'name' => [
                        new Required(),
                        new Length(min: 1, max: 50),
                    ],
                    'age' => new Integer(min: 18, max: 100),
                ],
                'files.*.url' => new Url(),
            ]),
        ];
    }
}

$post = new Post(title: 'Hello, world!');
$postRulesProvider = new PostRulesProvider();
$validator = (new Validator())->validate($post, $postRulesProvider);
```

For rules without relations, instead of using `Composite` directly, create a child class that extends from it and put the 
rules into it. Don't forget to add the attribute support.

```php
use Attribute;
use Yiisoft\Validator\Rule\Composite;
use Yiisoft\Validator\Rule\Count;
use Yiisoft\Validator\Rule\Each;
use Yiisoft\Validator\Rule\Integer;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
final class RgbColorRuleSet extends Composite
{
    public function getRules(): array
    {
        return [
            new Count(3),
            new Each([new Integer(min: 0, max: 255)])
        ];
    }
}

final class User
{
    public function __construct(
        private string $name,
        #[RgbColorRuleSet]
        private array $avatarBackgroundColor,
    ) {
    }
}
```

The `Nested` rule can be used without arguments, see this [example](#configuring-for-a-single-entity--model) above.

### Callables

Attempting to use callables within the scope of an attribute will cause the error. This means that using [when] for
[conditional validation] or `callback` argument for the `Callback` rule will not work. 

The workarounds are:

- `Composite` or the rules provider described in the [Instances] section will also fit here.
- Create a [custom rule].
- For the `Callback` rule in particular, it is possible to replace a callback with a [method reference].

### Function / method calls

Both function and method calls are not supported within the scope of an attribute. If the intent is to call a function / 
method for validation - use a `Callback` rule with [method reference]. Otherwise, the remaining options are:

- Use `Composite` or rules provider described in the [Instances] section.
- Create a [custom rule].

## Using rules

Well, the rules are configured. What's next? We can either:

- Pass them for validation right away.
- Tune the parsing of rules (skippable properties, using cache).
- Use them for something else (e.g. for exporting their options).

Let's use a blog post again for demonstration, but a slightly shortened version:

```php
use Yiisoft\Validator\Rule\Integer;
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\Rule\Nested;
use Yiisoft\Validator\Rule\Required;

final class Post
{
    public function __construct(
        #[Length(min: 1, max: 255)]
        private string $title,

        #[Nested(Author::class)]
        private Author|null $author,
    ) {
    }
}

final class Author
{
    public function __construct(
        #[Required]
        #[Length(min: 1, max: 50)]
        private string $name,

        #[Integer(min: 18, max: 100)]
        private int $age,
    ) {
    }
}
```

### Passing along with data for validation

Probably one of the cleanest ways is to pass DTO instances with declared rules and data. This way doesn't require
any additional setup:

```php
use Yiisoft\Validator\Validator;

$post = new Post(
    title: 'Hello, world!',
    author: new Author(
        name: 'John',
        age: 18,
    ),
);
$result = (new Validator())->validate($post) // Note `$rules` argument is `null` here.
```

### Passing separately for validation

It can be helpful to use the class for parsing rules and provide data separately:

```php
use Yiisoft\Validator\Validator;

$data = [
    'title' => 'Hello, world!',
    'author' => [
        'name' => 'John',
        'age' => 18,
    ],
];
$result = (new Validator())->validate($data, Post::class);
```

The data doesn't have to be within an array, the goal of this snippet is to show that it is isolated from the rules.

### Tuning parsing of rules

Data passed for validation as an object will be automatically normalized to `ObjectDataSet`. However, you can manually
wrap validated object with this set to allow some additional configuration:

```php
use Yiisoft\Validator\DataSet\ObjectDataSet;
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\Validator;

final class Post
{
    // Will be skipped from parsing rules declared via PHP attributes.
    private $author;

    public function __construct(
        #[Length(min: 1, max: 255)]
        public string $title,

        #[Length(min: 1)]
        protected $content,
    ) {
    }
}

$post = new Post(title: 'Hello, world!', content: 'Test content.');
$dataSet = new ObjectDataSet(
    $post,
    propertyVisibility: ReflectionProperty::IS_PUBLIC | ReflectionProperty::IS_PROTECTED,
    useCache: false,
);
$result = (new Validator())->validate($dataSet);
```

Some edge cases, like skipping DTO's static properties, require using of `AttributeRulesProvider`. After initializing it 
can be passed for validation right away - no need to extract rules manually beforehand.

```php
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\RulesProvider\AttributesRulesProvider;
use Yiisoft\Validator\Validator;

final class Post
{
    // Will be skipped from parsing rules declared via PHP attributes.
    private static $cache = [];

    public function __construct(
        #[Length(min: 1, max: 255)]
        private string $title,
    ) {
    }
}

$post = new Post(title: 'Hello, world!');
$rules = new AttributesRulesProvider(Post::class, skipStaticProperties: true);
$validator = (new Validator())->validate($post, $rules);
```

### Using rules outside the validator scope

Let's say we want to extract all rules for exporting their options to the client side for further implementing frontend 
validation:

```php
use Yiisoft\Validator\Helper\RulesDumper;
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\RulesProvider\AttributesRulesProvider;
use Yiisoft\Validator\Validator;

final class Post
{
    public function __construct(
        #[Length(min: 1, max: 255)]
        private string $title,
    ) {
    }
}

// The rules need to be extracted manually first.
$rules = (new AttributesRulesProvider(Post::class))->getRules();
$validator = (new Validator())->validate([], $rules);
$options = RulesDumper::asArray($rules);
```

[Attributes]: https://www.php.net/manual/en/language.attributes.overview.php
[DTO]: https://en.wikipedia.org/wiki/Data_transfer_object
[constructor property promotion]: https://www.php.net/manual/en/language.oop5.decon.php#language.oop5.decon.constructor.promotion
[readonly properties]: https://www.php.net/manual/en/language.oop5.properties.php#language.oop5.properties.readonly-properties
[Nested]: built-in-rules-nested.md
[Each]: built-in-rules-each.md
[Rector]: https://github.com/rectorphp/rector
[when]: conditional-validation.md#when
[conditional validation]: conditional-validation.md
[Instances]: #instances
[custom rule]: creating-custom-rules.md
[method reference]: built-in-rules-callback.md#for-property
