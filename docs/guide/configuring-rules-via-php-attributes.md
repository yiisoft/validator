# Configuring rules via PHP attributes

[Attributes] feature introduced in PHP 8 allowed to add an alternative way of configuring rules to this package. When
entities / models with their relations are represented as [DTO] classes, attributes make possible to use such classes 
for providing rules. The rules are defined near the properties themselves which some can find more convenient in terms
of perception.

## Configuring for a single entity / model

Given a single `User` entity / model:

```php
use Yiisoft\Validator\Rule\HasLength;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Rule\Required;

[
    'name' => [
        new Required(),
        new HasLength(min: 1, max: 50),
    ],
    'age' => [
        new Number(integerOnly: true, min: 18, max: 100),
    ],
]
```

the PHP attributes equivalent will be:

```php
use JetBrains\PhpStorm\Deprecated;
use Yiisoft\Validator\Rule\HasLength;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Rule\Required;

final class User
{
    public function __construct(
        // Multiple attributes.
        #[Required]
        #[HasLength(min: 1, max: 50)]
        // Can be combined with other attributes not related with rules.
        #[Deprecated]
        private readonly string $name,
        // Single attribute.
        #[Number(integerOnly: true, min: 18, max: 100)]
        private readonly int $age,
    ) {
    }
}
```

This example uses [constructor property promotion] feature, also introduced in PHP 8, but attributes can be used with 
regular properties as well:

```php
use Yiisoft\Validator\Rule\HasLength;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Rule\Required;

final class User
{
    // Multiple attributes.
    #[Required]
    #[HasLength(min: 1, max: 50)]
    public readonly string $name;

    // Single attribute.
    #[Number(integerOnly: true, min: 18, max: 100)]
    public readonly int $age;
}
```

> **Note:** [readonly properties] are supported only starting from PHP 8.1.

## Configuring for multiple entities / models with relations

Given an example of rule set for a blog post configured via arrays only:

```php
use Yiisoft\Validator\Rule\Each;
use Yiisoft\Validator\Rule\HasLength;
use Yiisoft\Validator\Rule\Nested;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\Rule\Url;

[
    new Nested([
        'title' => [
            new HasLength(min:1, max: 255),
        ],
        // One-to-one relation.
        'author' => new Nested([
            'name' => [
                new Required(),
                new HasLength(min: 1, max: 50),
            ],
            'age' => [
                new Number(integerOnly: true , min: 18, max: 100),
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

it can be applied to DTO classes like this achieving the same effect:

```php
use Yiisoft\Validator\Rule\Each;
use Yiisoft\Validator\Rule\HasLength;
use Yiisoft\Validator\Rule\Nested;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\Rule\Url;

final class Post
{
    #[HasLength(min: 1, max: 255)]
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
    #[HasLength(min: 1, max: 50)]
    public string $name;

    #[Number(integerOnly: true, min: 18, max: 100)]
    public int $age;
}

// Some rules, like "Nested" can be also configured through the class attribute.

#[Nested(['url' => new Url()])]
final class File
{
    public string $url;
}
```

For better understanding of relations concept, it's recommended to read [Nested] and [Each] guides.

## Traits

Attributes can be used in traits as well. It might come in handy for reusing the same set of properties with identical 
rules: 

```php
use Yiisoft\Validator\Rule\HasLength;

trait TitleTrait
{
    #[HasLength(max: 255)]
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
use Yiisoft\Validator\Rule\HasLength;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Rule\Required;

class Car
{
    #[Required]
    #[HasLength(min: 1, max: 50)]
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

In this case the set of rules for `Truck` will be:

```php
use Yiisoft\Validator\Rule\BooleanValue;
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
- All parent rules for properties not overridden in the child class are obtained fully.

As for the data, default values set in the child class take precedence.

## Adding attributes support to custom rules

In order for rules to be attached to DTO properties or the whole DTO - the `Attribute` attribute must be added to the 
custom class. And in order for rules to be fetched from attributes, they must implement the `RuleInterface`.

Example for `Composite`:

```php
use Attribute;
use Yiisoft\Validator\Rule\Composite;
use Yiisoft\Validator\Rule\Count;
use Yiisoft\Validator\Rule\Each;
use Yiisoft\Validator\Rule\Number;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
final class RgbColorRuleSet extends Composite
{
    public function getRules(): array
    {
        return [
            new Count(exactly: 3),
            new Each([new Number(integerOnly: true, min: 0, max: 255)])
        ];
    }
}
```

Example for custom rule:

```php
use Yiisoft\Validator\RuleInterface;

final class Yaml implements RuleInterface
{
    public function __construct(
        public string $incorrectInputMessage = 'Value must be a string. {type} given.',
        public string $message = 'The value is not a valid YAML.',
    ) {
    }

    public function getName(): string
    {
        return 'yaml';
    }

    public function getHandler(): string
    {
        return YamlHandler::class;
    }
}
```

To allow attaching to the class, modify attribute definition like so:

```php
use Attribute;
use Yiisoft\Validator\RuleInterface;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
final class MyCustomRule implements RuleInterface 
{
    // ...
}
```

## Limitations and workarounds

### Instances

Passing instances in attributes' scope is only possible since PHP 8.1. That means using attributes for complex rules, such as 
`Composite`, `Each` and `Nested` or rules taking instances as arguments, with PHP 8.0 can be problematic.

The first workaround is to upgrade to PHP 8.1 - this is not that hard as upgrading the major version. Tools like 
[Rector] can ease the process of upgrading the code base by automating routine tasks.

If this is not an option, use the other ways of providing rules - via rules providers, for example:

```php
use Yiisoft\Validator\Rule\HasLength;
use Yiisoft\Validator\Rule\Nested;
use Yiisoft\Validator\Rule\Number;
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
                'title' => new HasLength(min:1, max: 255),
                'author' => [
                    'name' => [
                        new Required(),
                        new HasLength(min: 1, max: 50),
                    ],
                    'age' => new Number(integerOnly: true , min: 18, max: 100),
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

For rules without relations, instead of using `Composite` directly - create a child class extending from it and put the 
rules there. Don't forget to add the attribute support.

```php
use Attribute;
use Yiisoft\Validator\Rule\Composite;
use Yiisoft\Validator\Rule\Count;
use Yiisoft\Validator\Rule\Each;
use Yiisoft\Validator\Rule\Number;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
final class RgbColorRuleSet extends Composite
{
    public function getRules(): array
    {
        return [
            new Count(exactly: 3),
            new Each([new Number(integerOnly: true, min: 0, max: 255)])
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

The `Nested` rule can be used with no arguments, see this [example](#configuring-for-a-single-entity--model) above.

### Callables

An attempt to use callables within an attribute's scope will cause the error. This means using [when] for [conditional 
validation] or `callback` argument for `Callback` rule will not work. 

The workarounds are:

- `Composite` or rules provider described in [Instances] section will also fit here.
- Create a [custom rule].
- For `Callback` rule specifically there is possibility to replace a callback with a [method reference].

### Function / method calls

Both function and method calls are not supported within an attribute's scope. If the intent is to call a function / 
method for validation - use a `Callback` rule with [method reference]. Otherwise, the remaining options are:

- Use `Composite` or rules provider described in [Instances] section.
- Create a [custom rule].

## Using rules

Well, the rules are configured. What's next? We can either:

- Pass them for validation right away.
- Tune parsing of rules (skippable properties, using cache).
- Use them for something else (e.g. for exporting their options).

Let's use a blog post again for demonstration, but a slightly shortened version:

```php
use Yiisoft\Validator\Rule\HasLength;
use Yiisoft\Validator\Rule\Nested;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Rule\Required;

final class Post
{
    public function __construct(
        #[HasLength(min: 1, max: 255)]
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
        #[HasLength(min: 1, max: 50)]
        private string $name,

        #[Number(integerOnly: true, min: 18, max: 100)]
        private int $age,
    ) {
    }
}
```

### Passing along with data for validation

Probably one of the most neat ways is to pass DTO instances with declared rules filled with data without any additional 
setup:

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

Sometimes, vice versa, it can be helpful to use the class only for parsing rules and provide data separately:

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

The data doesn't have to be within array, the goal of this snippet is to show that is isolated from the rules.

### Tuning parsing of rules

Data passed for validation as an object will be automatically normalized to `ObjectDataSet`. However, you can manually
wrap validated object with this set to allow some additional configuration:

```php
use Yiisoft\Validator\DataSet\ObjectDataSet;
use Yiisoft\Validator\Rule\HasLength;
use Yiisoft\Validator\Validator;

final class Post
{
    // Will be skipped from parsing rules declared via PHP attributes.
    private $author;

    public function __construct(
        #[HasLength(min: 1, max: 255)]
        public string $title,

        #[HasLength(min: 1)]
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
use Yiisoft\Validator\Rule\HasLength;
use Yiisoft\Validator\RulesProvider\AttributesRulesProvider;
use Yiisoft\Validator\Validator;

final class Post
{
    // Will be skipped from parsing rules declared via PHP attributes.
    private static $cache = [];

    public function __construct(
        #[HasLength(min: 1, max: 255)]
        private string $title,
    ) {
    }
}

$post = new Post(title: 'Hello, world!');
$rules = new AttributesRulesProvider(Post::class, skipStaticProperties: true);
$validator = (new Validator())->validate($post, $rules);
```

### Using rules outside the validator scope

Let's say we want to extract all rules for exporting their options to client side for further implementing frontend 
validation:

```php
use Yiisoft\Validator\Helper\RulesDumper;
use Yiisoft\Validator\Rule\HasLength;
use Yiisoft\Validator\RulesProvider\AttributesRulesProvider;
use Yiisoft\Validator\Validator;

final class Post
{
    public function __construct(
        #[HasLength(min: 1, max: 255)]
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
[Rector]: https://github.com/rectorphp/rector
[when]: conditional-validation.md#when
[conditional validation]: conditional-validation.md
[Instances]: #instances
[custom rule]: creating-custom-rules.md
[method reference]: built-in-rules-callback.md#for-property
