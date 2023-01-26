# Configuring rules via PHP attributes

[Attributes] feature introduced in PHP 8 allowed to add an alternative way of configuring rules to this package. When 
entities / models with their relations are represented as [DTO] classes, attributes make possible to use them for 
providing rules.

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
use Yiisoft\Validator\Rule\Required;

final class Author
{        
    public function __construct(
        // Multiple attributes.
        #[Required]
        #[HasLength(min: 1, max: 50)]
        private readonly string $name;
        
        // Single attribute.
        #[Number(integerOnly: true, min: 18, max: 100)]
        private readonly int $age;
    )      
}
```

This example uses [constructor property promotion] feature, also introduced in PHP 8, but attributes can be used with 
regular properties as well:

```php
use Yiisoft\Validator\Rule\Required;

final class Author
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
use Yiisoft\Validator\Rule\Number;

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
    ]);
];
```

it can be applied to DTO classes like this achieving the same effect:

```php
use Yiisoft\Validator\Rule\Nested;
use Yiisoft\Validator\Rule\Required;

final class Post
{
    #[HasLength(min: 1, max: 255)]
    public string $title;
    
    // "Nested" can be used without arguments, but make sure to fill the value with the instance in this case (here it's
    // filled right in the constructor).
    #[Nested]
    public Author|null $author = null;
    
    // Passing instances is available only since PHP 8.1.
    #[Each(new Nested(File::class)]
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

Traits are supported too. They might come in handy for reusing the same set of properties with identical rules: 

```php
use Yiisoft\Validator\Rule\HasLength;

trait TitleTrait
{
    #[HasLength(max: 255)]
    public string $title;
}

final class Post
{
    use TitleTrait;
}

final class WikiArticle
{
    use TitleTrait;
}
```

## Using rules

Well, the rules are configured. What's next? We can either:

- Pass them for validation right away.
- Filter properties for parsing rules first.
- Use them for something else (e.g. for exporting their options).

Let's use a blog post again for demonstration, but a slightly shortened version:

```php
use Yiisoft\Validator\Rule\Required;

final class Post
{        
    public function __construct(
        #[HasLength(min: 1, max: 255)]
        private string $title;
        
        #[Nested(Author::class)]
        private Author|null $author;              
    ) {       
    }
}

final class Author
{        
    public function __construct(
        #[Required]
        #[HasLength(min: 1, max: 50)]
        private string $name;
        
        #[Number(integerOnly: true, min: 18, max: 100)]
        private int $age;
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

### Attribute rules provider

To extract rules manually, use `AttributeRulesProvider`. Below are some examples showing what it can be helpful for.

#### Tuning skippable properties

For example, for fine-tuning to solve some edge cases - to skip DTO's static properties in particular:

```php
use Yiisoft\Validator\RulesProvider\AttributesRulesProvider;
use Yiisoft\Validator\Validator;

final class Post
{   
    // Will be skipped from parsing rules declared via PHP attributes.
    private static $cache = [];
    
    public function __construct(
        #[HasLength(min: 1, max: 255)]
        private string $title;                      
    ) {       
    }
}

$rules = new AttributesRulesProvider(Post::class, skipStaticProperties: true)->getRules();
$validator = (new Validator())->validate([], $rules);
```

#### Using rules outside the validator scope

Let's say we want to extract all rules for exporting their options to client side for further implementing frontend 
validation:

```php
use Yiisoft\Validator\Helper\RulesDumper;
use Yiisoft\Validator\RulesProvider\AttributesRulesProvider;
use Yiisoft\Validator\Validator;

final class Post
{       
    public function __construct(
        #[HasLength(min: 1, max: 255)]
        private string $title;                      
    ) {       
    }
}

$rules = new AttributesRulesProvider(Post::class, skipStaticProperties: true)->getRules();
$validator = (new Validator())->validate([], $rules);
$options = (new RulesDumper())->asArray($rules);
```

[Attributes]: https://www.php.net/manual/en/language.attributes.overview.php
[DTO]: https://en.wikipedia.org/wiki/Data_transfer_object
[constructor property promotion]: https://www.php.net/manual/en/language.oop5.decon.php#language.oop5.decon.constructor.promotion
[readonly properties]: https://www.php.net/manual/en/language.oop5.properties.php#language.oop5.properties.readonly-properties
