# Использование валидатора

Валидатор позволяет проверить данные в любом формате. Вот некоторые из наиболее распространенных случаев использования.

## Данные

### Одиночное значение

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

> **Примечание:** Используйте правило [Each] для валидации нескольких значений одного типа.

### Массив

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
    // Правила, не относящиеся к конкретному атрибуту.
 
    // Хотя бы один из атрибутов ("email" или "phone"), должен быть передан и иметь непустое значение.
    new AtLeast(['email', 'phone']),

    // Правила, относящиеся к конкретному атрибуту.

    'name' => [
        // Атрибут "name" обязательный (должен быть передан и иметь непустое значение).
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

Подобно массивам, объект можно провалидировать как в целом, так и по отдельным свойствам.

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

> **Примечание:** [readonly-свойства] поддерживаются только начиная с версии PHP 8.1.

> **Примечание:** Используйте правило [Nested] для валидации связанных объектов и правило [Each] для валидации нескольких объектов.

### Пользовательский набор данных

В большинстве случаев создание собственного набора данных не требуется из-за наличия встроенных и автоматической нормализации всех типов во время валидации.
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

Может помочь повторно использовать один и тот же набор правил в разных местах. Возможны два способа: использование атрибутов и явное указание через реализацию метода интерфейса.

#### Использование атрибутов

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

#### Использование реализации метода интерфейса

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

### Передача правил через объект данных

В этом случае правила передаются в дополнение к данным в одном и том же объекте.
Поддерживается только реализация метода интерфейса.
Обратите внимание, что аргумент `rules` имеет значение `null` при вызове метода `validate()`.

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
[readonly-свойства]: https://www.php.net/manual/ru/language.oop5.properties.php#language.oop5.properties.readonly-properties
