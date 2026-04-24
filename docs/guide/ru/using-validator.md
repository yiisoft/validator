# Использование валидатора

Валидатор позволяет проверить данные в любом формате. Вот некоторые из
наиболее распространенных случаев использования.

## Данные

### Одиночное значение

В простейшем случае, валидатор может использоваться для проверки одиночного
значения:

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

> **Примечание:** Используйте правило [Each] для валидации нескольких значений одного типа.

### Массив

Валидировать массив можно как целиком, так и по отдельным
элементам. Например:

```php
use Yiisoft\Validator\Rule\FilledAtLeast;
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
    // Правила, не относящиеся к конкретному свойству.

    // Хотя бы одно из свойств ("email" или "phone"), должно быть передано и иметь непустое значение.  
    new FilledAtLeast(['email', 'phone']),

    // Правила, относящиеся к конкретному свойству.

    'name' => [
        // Свойство "name" обязательно (должно быть передано и иметь непустое значение).
        new Required(),
        // Длина "name" должна быть не менее 2 символов.
        new Length(min: 2),
    ],  
    'age' => new Number(min: 21), // Возраст должен быть не менее 21 года.  
    'email' => new Email(), // Email должен быть валидным адресом электронной почты.  
];
$result = (new Validator())->validate($data, $rules);
```

> **Примечание:** Используйте правило [Nested] для валидации вложенных массивов и правило [Each] для валидации нескольких массивов.

### Объект

Подобно массивам, объект можно провалидировать как в целом, так и по
отдельным свойствам.

Для объектов есть дополнительная возможность настроить валидацию по
атрибутами, что позволяет не передавать правила отдельно явным образом
(достаточно передавать только сам объект). Например:

```php
use Yiisoft\Validator\Rule\FilledAtLeast;
use Yiisoft\Validator\Rule\Email;
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\Validator;

#[FilledAtLeast(['email', 'phone'])]
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

> **Примечание:** [readonly-свойства] поддерживаются только начиная с версии PHP 8.1.

> **Примечание:** Используйте правило [Nested] для валидации связанных объектов и правило [Each] для валидации нескольких объектов.

### Пользовательский набор данных

В большинстве случаев создание собственного набора данных не требуется из-за
наличия встроенных и автоматической нормализации всех типов во время
валидации.
Однако, это может оказаться полезным, например, для изменения значения
по-умолчанию для определенных свойств:

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

    public function getPropertyValue(string $property): mixed
    {
        if ($this->hasProperty($property)) {
            return $this->data[$property];
        }

        return $property === 'name' ? '' : null;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function hasProperty(string $property): bool
    {
        return array_key_exists($property, $this->data);
    }
}

$data = new MyArrayDataSet([]);
$rules = ['name' => new Length(min: 2), 'age' => new Number(min: 21)];
$result = (new Validator())->validate($data, $rules);
```

## Правила

### Передача одиночного значения

Для одиночного правила есть возможность опустить массив:

```php
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Validator;

$value = 7;
$rule = new Number(min: 42);
$result = (new Validator())->validate($value, $rule);
```

### Передача правил посредством выделенного объекта

Может помочь повторно использовать один и тот же набор правил в разных
местах. Возможны два способа: использование атрибутов и явное указание через
реализацию метода интерфейса.

#### Использование атрибутов

В этом случае правила будут парситься автоматически, дополнительно ничего
делать не нужно.

```php
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Validator;

final class PersonRulesProvider
{
    #[Length(min: 2)]
    public string $name;

    #[Number(min: 21)]
    public int $age;
}

$data = ['name' => 'John', 'age' => 18];
$rulesProvider = new PersonRulesProvider();
$result = (new Validator())->validate($data, $rulesProvider);
```

#### Использование реализации метода интерфейса

When an object implementing `RulesProviderInterface` is passed as the
`$rules` argument (second argument of `validate()`), only the rules from
`getRules()` are used. PHP attributes on the object are not parsed in this
case.

```php
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\RulesProviderInterface;
use Yiisoft\Validator\Validator;

final class PersonRulesProvider implements RulesProviderInterface
{
    #[Length(min: 2)] // Ignored because the object is passed as the $rules argument.
    public string $name;

    #[Number(min: 21)] // Ignored because the object is passed as the $rules argument.
    public int $age;

    public function getRules(): iterable
    {
        return ['name' => new Length(min: 2), 'age' => new Number(min: 21)];
    }
}

$data = ['name' => 'John', 'age' => 18];
$rulesProvider = new PersonRulesProvider();
$result = (new Validator())->validate($data, $rulesProvider);
```

### Передача правил через объект данных

In this way, rules are provided in addition to data in the same object. Both
PHP attributes and `getRules()` method are supported — their rules are
merged (attribute rules are applied first). Note that the `rules` argument
is `null` in the `validate()` method call.

```php
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\RulesProviderInterface;
use Yiisoft\Validator\Validator;

final class Person implements RulesProviderInterface
{
    public function __construct(
        #[Length(min: 2)] // Merged with rules from getRules(). Attribute rules are applied first.
        public string $name = '',
        #[Number(min: 21)] // Merged with rules from getRules(). Attribute rules are applied first.
        public int $age = 0,
    ) {
    }

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
[readonly-свойства]: https://www.php.net/manual/ru/language.oop5.properties.php#language.oop5.properties.readonly-properties
