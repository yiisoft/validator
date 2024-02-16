# Using validator
# Использование валидатора

Validator allows to check data in any format. Here are some of the most common use cases.
Валидатор позволяет проверить данные в любом формате. Вот некоторые из наиболее распространенных случаев использования.

## Data
## Данные

### Single value
### Одиночное значение


In the simplest case, the validator can be used to check a single value:
В простейшем случае, валидатор может использоваться для проверки одиночного значения:

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

> **Note:** Use [Each] rule to validate multiple values of the same type.
> **Примечание:** Используйте правило [Each] для валидации нескольких значений одного типа.

### Array
### Массив

It's possible to validate an array both as a whole and by individual items. For example:
Валидировать массив можно как целиком, так и по отдельным элементам. Например:

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
    // Правила, не относящиеся к конкретному атрибуту.

    // At least one of the attributes ("email" and "phone") must be passed and have non-empty value.  
    // Хотя бы один из атрибутов ("email" или "phone"), должен быть передан и иметь непустое значение.
    new AtLeast(['email', 'phone']),

    // The rules related to a specific attribute.
    // Правила, относящиеся к конкретному атрибуту.

    'name' => [
        // The name is required (must be passed and have non-empty value).
        // Атрибут "name" обязательный (должен быть передан и иметь непустое значение).
        new Required(),
        // The name's length must be no less than 2 characters.
        // Длина "name" должна быть не менее 2 символов.
        new Length(min: 2),
    ],  
    'age' => new Number(min: 21), // Возраст должен быть не менее 21 года.  
    'email' => new Email(), // Email должен быть валидным адресом электронной почты.  
];
$result = (new Validator())->validate($data, $rules);
```

> **Note:** Use [Nested] rule to validate nested arrays and [Each] rule to validate multiple arrays.
> **Примечание:** Используйте правило [Nested] для валидации вложенных массивов и правило [Each] для валидации нескольких массивов.

### Object
### Объект

Similar to arrays, it's possible to validate an object both as a whole and by individual properties.
Подобно массивам, объект можно провалидировать как в целом, так и по отдельным свойствам.

For objects there is an additional option to configure validation with PHP attributes which allows to not pass the rules
separately in explicit way (passing just the object itself is enough). For example:
Для объектов есть дополнительная возможность настроить валидацию по атрибутами, что позволяет не передавать правила отдельно явным образом (достаточно передавать только сам объект). Например:

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

> **Note:** [readonly properties] are supported only starting from PHP 8.1.
> **Примечание:** [readonly-свойства] поддерживаются только начиная с версии PHP 8.1.


> **Note:** Use [Nested] rule to validate related objects and [Each] rule to validate multiple objects.
> **Примечание:** Используйте правило [Nested] для валидации связанных объектов и правило [Each] для валидации нескольких объектов.

### Custom data set
### Пользовательский набор данных

Most of the time creating a custom data set is not needed because of built-in data sets and automatic normalization of
all types during validation. However, this can be useful, for example, to change a default value for certain attributes:
В большинстве случаев создание собственного набора данных не требуется из-за наличия встроенных наборов данных и автоматической нормализации всех типов во время валидации.
Однако, это может оказаться полезным, например, для изменения значения по-умолчанию для определенных атрибутов:

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
## Правила

### Passing single rule
### Передача одиночного значения

For a single rule, there is an option to omit the array:
Для одиночного правила есть возможность опустить массив:

```php
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Validator;

$value = 7;
$rule = new Number(min: 42);
$result = (new Validator())->validate($value, $rule);
```

### Providing rules via dedicated object
### Передача правил посредством выделенного объекта

Could help reuse the same set of rules across different places. Two ways are possible - using PHP attributes 
and specifying explicitly via interface method implementation.
Может помочь повторно использовать один и тот же набор правил в разных местах. Возможны два способа: использование атрибутов и явное указание через реализацию метода интерфейса.


#### Using PHP attributes
#### Использование атрибутов

In this case, the rules will be automatically parsed, no need to additionally do anything.
В этом случае правила будут парситься автоматически, дополнительно ничего делать не нужно.

```php
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Validator;

final class PersonRulesProvider
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
#### Использование реализации метода интерфейса

Providing rules via interface method implementation has priority over PHP attributes. So, in case both are present,
the attributes will be ignored without causing an exception.
Передача правил через реализацию метода интерфейса имеет приоритет над атрибутами.
Поэтому в случае одновременного использования, атрибуты будут игнорироваться без выбрасывания исключения.

```php
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\RulesProviderInterface;
use Yiisoft\Validator\Validator;

final class PersonRulesProvider implements RulesProviderInterface
{
    #[Length(min: 2)] // Будет тихо проигнорировано.
    public string $name;

    #[Number(min: 21)] // Будет тихо проигнорировано.
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
### Передача правил через объект данных

In this way, rules are provided in addition to data in the same object. Only interface method implementation is 
supported. Note that the `rules` argument is `null` in the `validate()` method call.
В этом случае правила передаются в дополнение к данным в одном и том же объекте.
Поддерживается только реализация метода интерфейса.
Обратите внимание, что аргумент `rules` имеет значение `null`при вызове метода `validate()`.

```php
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\RulesProviderInterface;
use Yiisoft\Validator\Validator;

final class Person implements RulesProviderInterface
{
    #[Length(min: 2)] // Не поддерживается для использования с объектами данных. Будет тихо проигнорировано.
    public string $name;

    #[Number(min: 21)] // Не поддерживается для использования с объектами данных. Будет тихо проигнорировано.
    protected int $age;
    
    public function getRules(): iterable
    {
        return ['name' => new Length(min: 2), 'age' => new Number(min: 21)];
    }
}

$data = new Person(name: 'John', age: 18);
$result = (new Validator())->validate($data);
```

[Each]: built-in-rules-each.md
[Nested]: built-in-rules-nested.md
[readonly-свойства]: https://www.php.net/manual/en/language.oop5.properties.php#language.oop5.properties.readonly-properties
