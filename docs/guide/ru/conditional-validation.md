# Условия валидации

Правила содержат несколько вариантов их пропуска при определенных условиях. Не каждое правило поддерживает все эти варианты, но подавляющее большинство поддерживает.

## `skipOnError` - пропустить правило в наборе, если предыдущее не прошло проверку

По умолчанию, даже если при проверке атрибута возникает ошибка, обрабатываются все последующие правила в наборе.
Для изменения этого поведения используйте `skipOnError: true` для правил, которые необходимо пропустить:

В следующем примере проверка длины имени пропускается, если имя пользователя не заполнено.

```php
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Rule\Regex;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\Validator;

$data = [];
$rules = [
    'name' => [
        // Проверяется.
        new Required(),
        // Пропускается, поскольку "name" является обязательным, но не заполнено.
        new Length(min: 4, max: 20, skipOnError: true),
        // Проверяется, поскольку "skipOnError" по умолчанию "false". Установите значение "true", чтобы также пропустить его.
        new Regex('^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$'),
    ],
    'age' => [
        // Проверяется, так как "age" это другой атрибут со своим собственным набором правил.
        new Required(),
        // Проверяется, поскольку "skipOnError" по умолчанию "false". Установите значение "true", чтобы также пропустить его.
        new Number(min: 21),
    ],
];
$result = (new Validator())->validate($data, $rules);
```

Обратите внимание, что этот параметр должен быть установлен для каждого правила, которое необходимо пропустить в случае ошибки.

Такого же эффекта можно добиться с помощью правил `StopOnError` и `Composite`, которые могут быть более удобны для большого количества правил.

Использование `StopOnError`:

```php
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\Rule\Regex;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\Rule\StopOnError;
use Yiisoft\Validator\Validator;

$data = [];
$rules = [
    'name' => new StopOnError([
        new Required(),
        new Length(min: 4, max: 20),
        new Regex('^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$'),
    ]),
];
$result = (new Validator())->validate($data, $rules);
```

Использование `Composite`:

```php
use Yiisoft\Validator\Rule\Composite;
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\Rule\Regex;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\Validator;

$data = [];
$rules = [
    'name' => [
        new Required(),
        new Composite(
            [
                new Length(min: 4, max: 20),
                new Regex('^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$'),
            ],
            skipOnError: true,
        )
    ],
];
$result = (new Validator())->validate($data, $rules);
```

## `skipOnEmpty` - пропуск правила, если проверяемое значение "пустое"

По умолчанию, отсутствующие/пустые значения атрибутов проверяются. Если значение отсутствует, предполагается, что оно равно `null`.
Если вы хотите, чтобы атрибут был необязательным, используйте `skipOnEmpty: true`.

Пример с необязательным атрибутом "language":

```php
use Yiisoft\Validator\Rule\In;
use Yiisoft\Validator\Validator;

$data = [];
$rules = [
    'language' => [
        new In(['ru', 'en'], skipOnEmpty: true),
    ],
];
$result = (new Validator())->validate($data, $rules);
```

Если атрибут является обязательным, более уместно использовать `skipOnError: true` вместе с предшествующим правилом `Required`, вместо `skipOnEmpty: true`.
Это связано с тем, что обнаружение пустых значений в правиле `Required` и пропуск дальнейших правил можно настроить отдельно.

Более подробно об этом написано ниже, см. раздел [Configuring empty condition in other rules] !!!

```php
use Yiisoft\Validator\Rule\In;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\Validator;

$data = [];
$rules = [
    'language' => [
        new Required(),
        new In(['ru', 'en'], skipOnError: true),
    ],
];
$result = (new Validator())->validate($data, $rules);
```

### Основные сведения о "пустом условии"

Что считать пустым может варьироваться в зависимости от сферы использования.

Значение, передаваемое в `skipOnEmpty` называется "пустым условием".
Благодаря нормализации поддерживаются следующие сочетания:

- Когда `false` или `null`, `Yiisoft\Validator\EmptyCondition\NeverEmpty` автоматически используется в качестве обратного вызова - каждое значение
считается непустым и проверяется без пропуска (по умолчанию)
- Когда `true`, `Yiisoft\Validator\EmptyCondition\WhenEmpty` автоматически используется в качестве обратного вызова - только переданное (соответствующий атрибут должен присутствовать) и непустое значение (не `null`, `[]`, или `''`) проверяется.
- Если установлен пользовательский обратный вызов, он используется для проверки на пустоту.

`false` обычно больше подходит для  HTML-форм, а `true` - для API.

Есть еще несколько условий, для которых нет сокращений и они должны быть заданы явно, поскольку используются реже:

- `Yiisoft\Validator\EmptyCondition\WhenMissing` - значение считается пустым только в том случае, если оно отсутствует (не передается вообще).
- `Yiisoft\Validator\EmptyCondition\WhenNull` - ограничивает пустые значения только значением `null`.

Пример использования `WhenNull` в качестве параметра (обратите внимание, что передается экземпляр, а не имя класса):

```php
use Yiisoft\Validator\Rule\Integer;
use Yiisoft\Validator\EmptyCondition\WhenNull;

new Integer(max: 100, skipOnEmpty: new WhenNull());
```

### Пользовательское "пустое условие"

Для еще большей настройки, вы можете использовать свой собственный класс, реализующий магический метод `__invoke()`.
Вот пример, где значение пусто, только если оно отсутствует (при использовании атрибутов) или равно нулю:

```php
use Yiisoft\Validator\Rule\Number;

final class WhenZero
{
    public function __invoke(mixed $value, bool $isAttributeMissing): bool
    {
        return $isAttributeMissing || $value === 0;
    }
}

new Integer(max: 100, skipOnEmpty: new WhenZero());
```

или то же самое с помощью обратного вызова:

```php
use Yiisoft\Validator\Rule\Integer;

new Integer(
    max: 100,
    skipOnEmpty: static function (mixed $value, bool $isAttributeMissing): bool {
        return $isAttributeMissing || $value === 0;
    }
);
```

Преимущество использования класса заключается в возможности повторного использования кода.

### Использование одного и того же пользовательского "пустого условия" для всех правил

Для нескольких правил, это также может быть удобнее установить на уровне валидатора:

```php
use Yiisoft\Validator\RuleHandlerResolver\SimpleRuleHandlerContainer;
use Yiisoft\Validator\Validator;

$validator = new Validator(skipOnEmpty: true); // Использование сокращения.
$validator = new Validator(
    new SimpleRuleHandlerContainer(),
    // Использование пользовательского обратного вызова.
    skipOnEmpty: static function (mixed $value, bool $isAttributeMissing): bool {"age"
$rule = new Required(
    emptyCondition: static function (mixed $value, bool $isAttributeMissing): bool {
        return $isAttributeMissing || $value === '';
    },
);
```

### Настройка "пустого условия" в других правилах

Некоторые правила, такие как `Required` нельзя пропустить для пустых значений - это противоречит цели правила.

Однако здесь можно настроить пустое условие для определения того, когда значение пусто.
Обратите внимание - это не приводит к пропуску правила.
Это только определяет что является "пустым условием":

```php
use Yiisoft\Validator\Rule\Required;

$rule = new Required(
    emptyCondition: static function (mixed $value, bool $isAttributeMissing): bool {
        return $isAttributeMissing || $value === '';
    },
);
```

Также возможно установить его глобально для всех правил этого типа на уровне обработчика через `RequiredHandler::$defaultEmptyCondition`.

## `when`

`when` предоставляет возможность применить правило в зависимости от состояния обратного вызова.
Результат обратного вызова определяет, будет ли правило пропущено.
Сигнатура функции следующая:

```php
function (mixed $value, ValidationContext $context): bool;
```

где:

- `$value` проверяемое значение;
- `$context` контекст валидации;
- возвращаемое значение: `true` означает, что правило должно быть применено, а `false`, что его необходимо пропустить.

В этом примере штат требуется только в том случае, если страна - `Brazil`.
Метод `$context->getDataSet()->getAttributeValue()` позволяет вам получить значение любого другого атрибута в рамках `ValidationContext`.

```php
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\ValidationContext;
use Yiisoft\Validator\Validator;

$data = [];
$rules = [
    'country' => [
        new Required(),
        new Length(min: 2),
    ],
    'state' => new Required(
        when: static function (mixed $value, ValidationContext $context): bool {
            return $context->getDataSet()->getAttributeValue('country') === 'Brazil';
        },
    )
];
$result = (new Validator())->validate($data, $rules);
```

В качестве альтернативы функциям, можно использовать callable-классы.
Преимущество этого подхода заключается в возможности повторного использования кода.
Пример см. в разделе [Skip on empty]

[Configuring empty condition in other rules]: #configuring-empty-condition-in-other-rules
[Skip on empty]: #skiponempty---skipping-a-rule-if-the-validated-value-is-empty