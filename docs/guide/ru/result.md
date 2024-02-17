# Result
# Результат

The validation result is an object containing of errors occurred during validation.
Результатом валидации является объект, содержащий ошибки, возникшие во время проверки.

## Is validation successful?
## Валидация успешна?

To just check the status of validation (whether a data is valid as a whole), use the following `Result` API call:
Чтобы просто проверить статус валидации (валидны ли данные в целом), используйте следующий API-вызов `Result`:


```php
use Yiisoft\Validator\Result;

/** @var Result */
$result->isValid();
```

Его можно сузить до определенного атрибута:

```php
use Yiisoft\Validator\Result;

/** @var Result */
$result->isAttributeValid('name');
```

## Errors
## Ошибки

Most of the time telling only the status of validation is not enough. 
В большинстве случаев недостаточно указать только статус валидации.
There are multiple methods to get detailed errors list with their data from the result. 
Существует несколько способов получить подробный список ошибок с данными о них из результата.
The difference between them is in the grouping, filtering, and representation of every error.
Разница между ними заключается в группировке, фильтрации и представлении каждой ошибки.
Choose one to fit your needs depending on the situation.
В зависимости от ситуации выбирайте тот, который соответствует вашим потребностям.

### Flat list of error messages
### Плоский список сообщений об ошибках

One of the simplest cases is getting a flat list of all error messages. Use the following `Result` API call:
Одним из самых простых случаев является получение плоского списка всех сообщений об ошибках. Для этого используйте следующий API-вызов `Result`:

```php
use Yiisoft\Validator\Result;

/** @var Result */
$result->getErrorMessages();
```

An example of output with `age` and `email` attributes:
Пример вывода с атрибутами `age` и `email`:

```php
[
    'Value must be no less than 21.',
    'Значение должно быть не меньше 21.',
    'This value is not a valid email address.',
    'Значение не является валидным email адресом.',
    'A custom error message.',
    'Пользовательское сообщение об ошибке.',
];
```

It's easy to display and iterate, however, with a bigger amount of attributes and depending on a message, it can be
problematic to understand which attribute an error belongs to.
Его легко показывать и перебирать, однако при большом количестве атрибутов и в зависимости от сообщения, может быть проблематично понять, к какому атрибуту относится ошибка.

#### Error messages not bound to a specific attribute
#### Сообщения об ошибках, не привязанные к определенному атрибуту

Sometimes error messages are not related to a specific attribute. It can happen during the validation of
multiple attributes depending on each other for example. Use the following `Result` API call:
Иногда сообщения об ошибках не связаны с конкретным атрибутом.
Это может случиться, например, во время валидации нескольких атрибутов, зависящих друг от друга.
В таком случае используйте следующий API-вызов `Result`:


```php
$result->getCommonErrorMessages();
```

The output for example above:
Результат для примера выше:

```php
[
    'Пользовательское сообщение об ошибке.',
];
```

#### Filtering by a specific attribute
#### Фильтрация по определенному атрибуту

This list can be also filtered by a specific attribute. Only top-level attributes are supported.
Список также может быть отфильтрован по конкретному атрибуту. Поддерживаются атрибуты только верхнего уровня.

```php
$result->getAttributeErrorMessages('email');
```

The output for example above:
Результат для примера выше:

```php
[
    'This value is not a valid email address.',
];
```

### Error messages indexed by attribute
### Сообщения об ошибках, сгруппированные по атрибуту

To group error messages by attribute, use the following `Result` API call:
Для группировки сообщений об ошибках по атрибуту, используйте следующий API-вызов `Result`:

```php
use Yiisoft\Validator\Result;

/** @var Result */
$result->getErrorMessagesIndexedByAttribute();
```

An example of output:
Пример результата:

```php
[
    'name' => [
        'Value cannot be blank.',
        'This value must contain at least 4 characters.',
    ],
    'email' => ['This value is not a valid email address.'],
    // Error messages not bound to specific attribute are grouped under empty string key.
    // Сообщения об ошибках, не привязанные к конкретному атрибуту, группируются в ключе со значением пустая строка.
    '' => ['A custom error message.'],
];
```

Note that the result is always a 2-dimensional array with attribute names as keys at the first nesting level. 
Обратите внимание, что результатом всегда является двумерный массив с именами атрибутов в качестве ключей на первом уровне вложенности.
This means that further nesting of attributes is not supported (but could be achieved by using [`getErrorMessagesIndexedByPath()`](#error-messages-indexed-by-path)).
Это означает, что дальнейшее вложение атрибутов не поддерживается (но может быть достигнуто с помощью [`getErrorMessagesIndexedByPath()`](#error-messages-indexed-by-path)).
Returning to the previous example, when `name` and `email` belong to a `user` attribute, the output will be:
Возвращаясь к предыдущему примеру, когда `name` и `email` принадлежат атрибуту `user`, выходные данные будут такими

```php
[
    'user' => [
        'Value cannot be blank.',
        'This value must contain at least 4 characters.',
        'This value is not a valid email address.'
    ],
    // Error messages not bound to specific attribute are grouped under empty string key.
    // Сообщения об ошибках, не привязанные к конкретному атрибуту, группируются в ключе со значением пустая строка.
    '' => ['A custom error message.'],
];
```

Also keep in mind that attribute names are always strings, even when used with `Each`:
Также имейте в виду, что имена атрибутов всегда являются строками, даже если они используются с `Each`:

```php
$rule = new Each([new Number(min: 21)]),
```
ОШИБКА!!!!!????
Given `[21, 22, 23, 20]` input, the output will be: 
Передавая `[21, 22, 23, 20]` input, результат будет следующим: 

```php
[
    '1' => ['Value must be no less than 21.'],
    '2' => ['Value must be no less than 21.'],
],
```

### Error messages indexed by path
### Сообщения об ошибках, сгруппированные по пути

This is probably the most advanced representation offered by built-in methods. 
Вероятно, это самое продвинутое представление, предлагаемое встроенными методами.
The grouping is done by path - a 
concatenated attribute sequence showing the location of errored value within a data structure.
Группировка осуществляется по пути - объединенной последовательности атрибутов, показывающей расположение ошибочного значения в структуре данных.
A separator is customizable, 
dot notation is set as the default one. Use the following `Result` API call:
Разделитель настраивается, точечная нотация используется по-умолчанию. Используйте следующий API-вызов `Result`:


```php
use Yiisoft\Validator\Result;

/** @var Result */
$result->getErrorMessagesIndexedByPath();
```

An example of output:
Пример результата:

```php
[
    'user.firstName' => ['Value cannot be blank.'],
    'user.lastName' => ['This value must contain at least 4 characters.'],
    'email' => ['This value is not a valid email address.'],
    // Error messages not bound to specific attribute are grouped under empty string key.
    // Сообщения об ошибках, не привязанные к конкретному атрибуту, группируются в ключе со значением пустая строка.
    '' => ['A custom error message.'],
];
```

A path can contain integer elements too (when using the `Each` rule for example):
Путь также может содержать целочисленные элементы (например при использовании правила `Each`)

```php
[
    'charts.0.points.0.coordinates.y' => ['Value must be no greater than 10.'],
];
```

#### Resolving special characters collision in attribute names
#### Разрешение конфликтов специальных символов в именах атрибутов

When the attribute name in the error messages list contains a path separator (dot `.` by default),
it is automatically escaped using a backslash (`\`):
Если имя атрибута в списке сообщений об ошибках содержит разделитель пути (по умолчанию точка `.`) он автоматически экранируется обратной косой чертой (`\`):

```php
[
    'country\.code' => ['Value cannot be blank.'],
],
```

In case of using a single attribute per rule set, any additional modifications of attribute names in the rules
configuration are not required, so they must stay as is:
В случае использования одного атрибута в наборе правил любые дополнительные изменения имен атрибутов в правилах конфигурации не требуются, поэтому они должны оставаться такими, как есть:

```php
use Yiisoft\Validator\Rule\In;
use Yiisoft\Validator\Rule\Required;

$rules = [
    'country.code' => [
        new Required();
        new In(['ru', 'en'], skipOnError: true),
    ],
];
```

However, when using the `Nested` rule with multiple attributes per rule set, special characters need to be escaped with 
a backslash (`\`) for value paths to be correct and to be possible to reverse them back from string to individual 
items.
Однако, при использовании правила `Nested` с несколькими атрибутами в каждом наборе правил, специальные символы необходимо экранировать с помощью обратной косой черты (`\`) для того, чтобы пути к значениям были корректными и можно было преобразовать их обратно из строки в индивидуальное значение. ??????

See the [Using keys containing separator/shortcut] section for more details.
Подробности смотрите в разделе [Using keys containing separator/shortcut].

This can be used as an alternative to using a custom separator.
Это можно использовать как альтернативу использования пользовательского разделителя.

#### Filtering by a specific attribute
#### Фильтрация по определенному атрибуту

This list can be also filtered by a specific attribute. Only top-level attributes are supported.
Список также может быть отфильтрован по конкретному атрибуту. Поддерживаются атрибуты только верхнего уровня.

```php
use Yiisoft\Validator\Result;

/** @var Result */
$result->getAttributeErrorMessagesIndexedByPath('user');
```

The output for example above:
Результат для примера выше:

```php
[
    'firstName' => ['Value cannot be blank.'],
    'lastName' => ['This value must contain at least 4 characters.'],
];
```

## Error objects list
## Список объектов ошибок

When even these representations are not enough, an initial unmodified list of error objects can be accessed via 
this method:
Когда даже этих представлений недостаточно, доступ к исходному немодифицированному списку объектов ошибок можно получить через этот метод:

```php
use Yiisoft\Validator\Result;

/** @var Result */
$result->getErrors();
```

Each error stores the following data:
Для каждой ошибки сохраняются следующие данные:

- Message. Either a simple message like `This value is wrong.` or a template with placeholders enclosed in curly braces 
(`{}`), for example: `Value must be no less than {min}.`.
- Сообщение. Любое простое сообщение типа `This value is wrong.` или шаблон с плейсхолдерами, заключенными в фигурные скобки, например:  `Value must be no less than {min}.`.
The actual formatting is done in `Validator` depending on
the configured translator.
Фактическое форматирование выполняется в `Validator` в зависимости от настроек.
- Template parameters for substitution during formatting, for example: `['min' => 7]`.
- Параметры шаблона для подстановки при форматировании, например: `['min' => 7]`.
- A path to a value within a checked data structure, for example: `['user', 'name', 'firstName']`.
- Путь к значению в проверяемой структуре данных, например: `['user', 'name', 'firstName']`.

### An example of an application
### Пример приложения

What this can be useful for? For example, to build a nested tree of error messages indexed by attribute names:
Чем это может быть полезно? Например, чтобы построить вложенное дерево сообщений об ошибках, сгруппированное по именам атрибутов:

```php
[
    'user' => [
        'name' => [
            'firstName' => ['Value cannot be blank.'],
            'lastName' => ['This value must contain at least 4 characters.'],
        ],
    ],
    'email' => ['This value is not a valid email address.'],    
];
```

It's intentionally not provided out of the box due to the complexity of iteration.
Это намеренно не предусмотрено из коробки из-за сложности итерации.
However, this can be useful for dumping 
as JSON and storing in logs for example.
Однако может быть полезно, например, для дампа JSON или сохранения в лог.

Debugging original error objects is also more convenient.
Отладка исходных объектов ошибок также становится более удобной.

### Filtering by a specific attribute
### Фильтрация по определенному атрибуту

This list can be also filtered by a specific attribute. Only top-level attributes are supported.
Список также может быть отфильтрован по конкретному атрибуту. Поддерживаются атрибуты только верхнего уровня.

```php
use Yiisoft\Validator\Result;

/** @var Result */
$result->getAttributeErrors('email');
```

[Using keys containing separator / shortcut]: #using-keys-containing-separator--shortcut
