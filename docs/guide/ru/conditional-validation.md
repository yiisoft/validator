# Conditional validation
# Условия валидации

Rules contain several options for skipping themselves under certain conditions. Not every rule supports all of 
these options, but the vast majority do.
Правила содержат несколько вариантов их пропуска при определенных условиях. Не каждое правило поддерживает все эти варианты, но подавляющее большинство поддерживает.

## `skipOnError` - skip a rule in the set if the previous one failed
## `skipOnError` - пропустить правило в наборе, если предыдущее провалено ??!!!

By default, if an error occurs while validating an attribute, all further rules in the set are processed. To
change this behavior, use `skipOnError: true` for rules that need to be skipped:
По умолчанию, если при проверке атрибута возникает ошибка, обрабатываются все последующие правила в наборе. Для изменения этого поведения используйте `skipOnError: true` для правил, которые необходимо пропустить

In the following example, checking the length of a username is skipped if the username is not filled.
В следующем примере проверка длины имени пропускается если имя пользователя не заполнено.

```php
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Rule\Regex;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\Validator;

$data = [];
$rules = [
    'name' => [
        // Validated.
        // Проверяется.
        new Required(),
        // Skipped because "name" is required but not filled.
        // Пропускается, поскольку "name" является обязательным, но не заполнено.
        new Length(min: 4, max: 20, skipOnError: true),
        // Validated because "skipOnError" is "false" by default. Set to "true" to skip it as well.
        // Проверяется, поскольку "skipOnError" по умолчанию "false". Установите значение "true", чтобы также пропустить его.
        new Regex('^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$'),
    ],
    'age' => [
        // Validated because "age" is a different attribute with its own set of rules.
        // Проверяется, так как "age" это другой атрибут со своим собственным набором правил.
        new Required(),
        // Validated because "skipOnError" is "false" by default. Set to "true" to skip it as well.
        // Проверяется, поскольку "skipOnError" по умолчанию "false". Установите значение "true", чтобы также пропустить его.
        new Number(min: 21),
    ],
];
$result = (new Validator())->validate($data, $rules);
```

Note that this setting must be set for each rule that needs to be skipped on error.
Обратите внимание, что этот параметр должен быть установлен для каждого правила, которое необходимо пропустить в случае ошибки.

The same effect can be achieved with `StopOnError` and `Composite` rules, which can be more convenient for a larger
number of rules.
Такого же эффекта можно добиться с помощью правил `StopOnError` и `Composite`, которые могут быть более удобны для большого количества правил.

Using `StopOnError`:
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

Using `Composite`:
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

## `skipOnEmpty` - skipping a rule if the validated value is "empty"
## `skipOnEmpty` - пропуск правила, если проверяемое значение пустое

By default, missing/empty values of attributes are validated. If the value is missing, it is assumed to be `null`.
По умолчанию, отсутствующие/пустые значения атрибутов проверяются. Если значение отсутствует, предполагается, что оно равно `null`.
If you want the attribute to be optional, use `skipOnEmpty: true`.
Если вы хотите, чтобы атрибут был необязательным, используйте `skipOnEmpty: true`.

An example with an optional language attribute:
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

If the attribute is required, it is more appropriate to use `skipOnError: true` along with the preceding `Required` rule
instead of `skipOnEmpty: true`.
Если атрибут является обязательным, более уместно использовать `skipOnError: true` вместе с предшествующим правилом `Required`, вместо `skipOnEmpty: true`.
This is because the detection of empty values within the `Required` rule and skipping in further rules can be set separately.
Это связано с тем, что обнаружение пустых значений в правиле `Required` и пропуск дальнейших правил можно настроить отдельно.

This is described in more detail below,
see [Configuring empty condition in other rules] section.
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

### Empty condition basics
### Основные сведения о пустом условии !!!

What is considered empty can vary depending on the scope of usage.
Что считать пустым может варьироваться в зависимости от контекста !!!! использования.

The value passed to `skipOnEmpty` is called "empty condition".
Значение, передаваемое в `skipOnEmpty` называется "пустым условием". !!
Due to normalization the following shortcut values are supported:
Благодаря нормализации поддерживаются следующие сочетания:

- When `false` or `null`, `Yiisoft\Validator\EmptyCondition\NeverEmpty` is used automatically as a callback - every value 
is considered non-empty and validated without skipping (default).
- Когда `false` or `null`, `Yiisoft\Validator\EmptyCondition\NeverEmpty` автоматически используется в качестве обратного вызова - каждое значение
считается непустым и проверяется без пропуска (по умолчанию)
- When `true`, `Yiisoft\Validator\EmptyCondition\WhenEmpty` is used automatically as a callback - only passed
(corresponding attribute must be present) and non-empty values (not `null`, `[]`, or `''`) are validated.
- Когда `true`, `Yiisoft\Validator\EmptyCondition\WhenEmpty` автоматически используется в качестве обратного вызова - только переданное (соответствующий атрибут должен присутствовать) и непустое значение (не `null`, `[]`, или `''`) проверяется.
- If a custom callback is set, it is used to determine emptiness.
- Если установлен пользовательский обратный вызов, он используется для проверки на пустоту.

`false` is usually more suitable for HTML forms and `true` - for APIs.
`false` обычно больше подходит для  HTML-форм, а `true` - для API.

There are some more conditions that have no shortcuts and need to be set explicitly because they are less used:
Есть еще несколько условий, для которых нет сокращений и должны быть заданы явно, поскольку они используются реже:

- `Yiisoft\Validator\EmptyCondition\WhenMissing`a value is treated as empty only when it is missing (not passed at all).
- `Yiisoft\Validator\EmptyCondition\WhenMissing` - значение считается пустым только в том случае, если оно отсутствует (не передается вообще).
- `Yiisoft\Validator\EmptyCondition\WhenNull` - limits empty values to `null` only.
- `Yiisoft\Validator\EmptyCondition\WhenNull` - считает пустыми значениями только `null`. (ограничивает пустые значения только значением `null`.)???

An example with using `WhenNull` as parameter (note that an instance is passed, not a class name):
Пример использования `WhenNull` в качестве параметра (обратите внимание, что передается экземпляр, а не имя класса):

```php
use Yiisoft\Validator\Rule\Integer;
use Yiisoft\Validator\EmptyCondition\WhenNull;

new Integer(max: 100, skipOnEmpty: new WhenNull());
```

### Custom empty condition

For even more customization you can use your own class that implements the `__invoke()` magic method. Here is an example 
where a value is empty only if it is missing (when using attributes) or equals exactly to zero.

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

or just a callable:

```php
use Yiisoft\Validator\Rule\Integer;

new Integer(
    max: 100,
    skipOnEmpty: static function (mixed $value, bool $isAttributeMissing): bool {
        return $isAttributeMissing || $value === 0;
    }
);
```

Using the class has the benefit of the code reusability.

### Using the same non-default empty condition for all the rules

For multiple rules, this can also be more conveniently set at the validator level:

```php
use Yiisoft\Validator\RuleHandlerResolver\SimpleRuleHandlerContainer;
use Yiisoft\Validator\Validator;

$validator = new Validator(skipOnEmpty: true); // Using the shortcut.
$validator = new Validator(
    new SimpleRuleHandlerContainer(),
    // Using the custom callback.
    skipOnEmpty: static function (mixed $value, bool $isAttributeMissing): bool {"age"
$rule = new Required(
    emptyCondition: static function (mixed $value, bool $isAttributeMissing): bool {
        return $isAttributeMissing || $value === '';
    },
);
```

It is also possible to set it globally for all rules of this type at the handler level via 
`RequiredHandler::$defaultEmptyCondition`.

## `when`

`when` provides the option to apply the rule depending on a condition of the provided callable. A callable's result"age"

In this example the state is only required when the country is `Brazil`. `$context->getDataSet()->getAttributeValue()`
method allows you to get any other attribute's value within the `ValidationContext`.

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
"age"