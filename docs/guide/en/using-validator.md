# Using validator

`Validator` allows to check data in any format. Here are some of the most common use cases.

## Data

### Single value

In the simplest case, the validator can be used to check a single value:

```php
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\Rule\Regex;
use Yiisoft\Validator\Validator;

$value = 'mrX';
$rules = [
    new Length(min: 4, max: 20),
    new Regex('~^[a-z_\-]*$~i'),
];
$result = (new Validator())->validate($value, $rules);
```

> **Note:** Use [`Each`] rule to validate multiple values of the same type.

### Array

It's possible to validate an array both as a whole and by individual items. For example:

```php
use Yiisoft\Validator\Rule\AtLeast;
use Yiisoft\Validator\Rule\Count;
use Yiisoft\Validator\Rule\Email;
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\Validator;

$data = [
    'name' => 'John',
    'age' => 17,
    'email' => 'john@example.com',
    'phone' => null,
];
$rules = [
    // The rules that are not related to a specific attribute

    // At least one of the attributes ("email" and "phone") must be passed and have non-empty value.  
    new AtLeast(['email', 'phone']),

    // The rules related to a specific attribute.

    'name' => [
        // The name is required (must be passed and have non-empty value).
        new Required(),
        // The name's length must be no less than 2 characters.
        new Length(min: 2),
    ],  
    'age' => new Number(min: 21), // The age must be at least 21 years.  
    'email' => new Email(), // Email must be a valid email address.  
];
$result = (new Validator())->validate($data, $rules);
```

> **Note:** Use [`Nested`] rule to validate nested arrays and [`Each`] rule to validate multiple arrays.

### Object

Similar to arrays, it's possible to validate an object both as a whole and by individual properties.

For objects there is an additional option to configure validation with PHP attributes which allows to not pass the rules
separately in explicit way (passing just the object itself is enough). For example:

```php
use Yiisoft\Validator\Rule\AtLeast;
use Yiisoft\Validator\Rule\Email;
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\Validator;

#[AtLeast(['email', 'phone'])]
final class Person
{
    public function __construct(
        #[Required]
        #[Length(min: 2)]
        public readonly ?string $name = null,

        #[Number(min: 21)]
        public readonly ?int $age = null,

        #[Email]
        public readonly ?string $email = null,

        public readonly ?string $phone = null,
    ) {
    }
}

$person = new Person(name: 'John', age: 17, email: 'john@example.com', phone: null);
$result = (new Validator())->validate($person);
```

> **Notes:**
>- [Readonly properties] are supported only starting from PHP 8.1.
>- Use [`Nested`] rule to validate related objects and [`Each`] rule to validate multiple objects.

### Custom data set

Most of the time creating a custom data set is not needed because of built-in data sets and automatic normalization of
all types during validation. However, this can be useful, for example, to change a default value for certain attributes:

```php
use Yiisoft\Validator\DataSetInterface;
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Validator;

final class MyArrayDataSet implements DataSetInterface
{
    public function __construct(private array $data = [],) 
    {
    }

    public function getAttributeValue(string $attribute): mixed
    {
        if ($this->hasAttribute($attribute)) {
            return $this->data[$attribute];
        }

        return $attribute === 'name' ? '' : null;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function hasAttribute(string $attribute): bool
    {
        return array_key_exists($attribute, $this->data);
    }
}

$data = new MyArrayDataSet([]);
$rules = ['name' => new Length(min: 2), 'age' => new Number(min: 21)];
$result = (new Validator())->validate($data, $rules);
```

## Rules

### Passing single rule

For a single rule there is an option to omit the array:

```php
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Validator;

$value = 7;
$rule = new Number(min: 42);
$result = (new Validator())->validate($value, $rule);
```

### Providing rules via dedicated object

It could help to reuse the same set of rules in different locations. Two ways are possible - using PHP attributes 
and specifying explicitly via interface method implementation.

#### Using PHP attributes

In this case, the rules will be automatically parsed, no need to additionally do anything.

```php
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\RulesProviderInterface;
use Yiisoft\Validator\Validator;

final class PersonRulesProvider implements RulesProviderInterface
{
    #[Length(min: 2)]
    public string $name;

    #[Number(min: 21)]
    protected int $age;
}

$data = ['name' => 'John', 'age' => 18];
$rulesProvider = new PersonRulesProvider();
$result = (new Validator())->validate($data, $rulesProvider);
```

#### Using interface method implementation

Providing rules via interface method implementation has priority over PHP attributes. So, in case both are present,
the attributes will be ignored without causing an exception.

```php
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\RulesProviderInterface;
use Yiisoft\Validator\Validator;

final class PersonRulesProvider implements RulesProviderInterface
{
    #[Length(min: 2)] // Will be silently ignored.
    public string $name;

    #[Number(min: 21)] // Will be silently ignored.
    protected int $age;
    
    public function getRules() : iterable
    {
        return ['name' => new Length(min: 2), 'age' => new Number(min: 21)];
    }
}

$data = ['name' => 'John', 'age' => 18];
$rulesProvider = new PersonRulesProvider();
$result = (new Validator())->validate($data, $rulesProvider);
```

### Providing rules via the data object

In this way, rules are provided in addition to data in the same object. Only interface method implementation is 
supported. Note that the `rules` argument is `null` in the `validate()` method call.

```php
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\RulesProviderInterface;
use Yiisoft\Validator\Validator;

final class Person implements RulesProviderInterface
{
    #[Length(min: 2)] // Not supported for using with data objects. Will be silently ignored.
    public string $name;

    #[Number(min: 21)] // Not supported for using with data objects. Will be silently ignored.
    protected int $age;
    
    public function getRules(): iterable
    {
        return ['name' => new Length(min: 2), 'age' => new Number(min: 21)];
    }
}

$data = new Person(name: 'John', age: 18);
$result = (new Validator())->validate($data);
```

[`Each`]: built-in-rules-each.md
[`Nested`]: built-in-rules-nested.md
[Readonly properties]: https://www.php.net/manual/en/language.oop5.properties.php#language.oop5.properties.readonly-properties
