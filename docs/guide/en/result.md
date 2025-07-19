# Result

The validation result is an object containing of errors occurred during validation.

## Is validation successful?

To just check the status of validation (whether a data is valid as a whole), use the following `Result` API call:

```php
use Yiisoft\Validator\Result;

/** @var Result */
$result->isValid();
```

It can be narrowed down to a specific property:

```php
use Yiisoft\Validator\Result;

/** @var Result $result */
$result->isPropertyValid('name');
```

## Errors

Most of the time telling only the status of validation is not enough. There are multiple methods to get detailed errors
list with their data from the result. The difference between them is in the grouping, filtering, and representation of every
error. Choose one to fit your needs depending on the situation.

### Flat list of error messages

One of the simplest cases is getting a flat list of all error messages. Use the following `Result` API call:


```php
use Yiisoft\Validator\Result;

/** @var Result */
$result->getErrorMessages();
```

An example of output with `age` and `email` properties:

```php
[
    'Value must be no less than 21.',
    'This value is not a valid email address.',
    'A custom error message.',
];
```

It's easy to display and iterate, however, with a bigger amount of properties and depending on a message, it can be
problematic to understand which property an error belongs to.

#### Error messages not bound to a specific property

Sometimes error messages are not related to a specific property. It can happen during the validation of
multiple properties depending on each other for example. Use the following `Result` API call:

```php
$result->getCommonErrorMessages();
```

The output for example above:

```php
[
    'A custom error message.',
];
```

#### Filtering by a specific property

This list can be also filtered by a specific property. Only top-level attributes are supported.

```php
$result->getPropertyErrorMessages('email');
```

The output for example above:

```php
[
    'This value is not a valid email address.',
];
```

#### Filtering by a specific path

This list of error messages can be filtered by a specific path to property.

```php
$result->getPropertyErrorMessagesByPath(['person', 'first_name']);
```

### Error messages indexed by property

To group error messages by property, use the following `Result` API call:

```php
use Yiisoft\Validator\Result;

/** @var Result */
$result->getErrorMessagesIndexedByProperty();
```

An example of output:

```php
[
    'name' => [
        'Value cannot be blank.',
        'This value must contain at least 4 characters.',
    ],
    'email' => ['This value is not a valid email address.'],
    // Error messages not bound to specific property are grouped under empty string key.
    '' => ['A custom error message.'],
];
```

Note that the result is always a 2-dimensional array with property names as keys at the first nesting level. This means
that further nesting of properties is not supported (but could be achieved
by using [`getErrorMessagesIndexedByPath()`](#error-messages-indexed-by-path)).
Returning to the previous example, when `name` and `email` belong to a `user` property, the output will be:

```php
[
    'user' => [
        'Value cannot be blank.',
        'This value must contain at least 4 characters.',
        'This value is not a valid email address.'
    ],
    // Error messages not bound to specific property are grouped under empty string key.
    '' => ['A custom error message.'],
];
```

Also keep in mind that property names must be strings, even when used with `Each`:

```php
$rule = new Each([new Number(min: 21)]),
```

With input containing non-string keys for top level properties, for example, `[21, 22, 23, 20]`,
`InvalidArgumentException` will be thrown.

Even array `['1' => 21, '2' => 22, '3' => 23, '4' => 20]` will cause an error, because PHP [will cast keys to the int type].

But if given array with string keys `['1a' => 21, '2b' => 22, '3c' => 23, '4d' => 20]`, the output will be:

```php
[
    '4d' => [
        0 => 'Value must be no less than 21.'
    ]
]
```

### Error messages indexed by path

This is probably the most advanced representation offered by built-in methods. The grouping is done by path - a
concatenated property sequence showing the location of errored value within a data structure. A separator is customizable,
dot notation is set as the default one. Use the following `Result` API call:

```php
use Yiisoft\Validator\Result;

/** @var Result */
$result->getErrorMessagesIndexedByPath();
```

An example of output:

```php
[
    'user.firstName' => ['Value cannot be blank.'],
    'user.lastName' => ['This value must contain at least 4 characters.'],
    'email' => ['This value is not a valid email address.'],
    // Error messages not bound to specific property are grouped under empty string key.
    '' => ['A custom error message.'],
];
```

A path can contain integer elements too (when using the `Each` rule for example):

```php
[
    'charts.0.points.0.coordinates.y' => ['Value must be no greater than 10.'],
];
```

#### Resolving special characters collision in property names

When the property name in the error messages list contains a path separator (dot `.` by default),
it is automatically escaped using a backslash (`\`):

```php
[
    'country\.code' => ['Value cannot be blank.'],
],
```

In case of using a single property per rule set, any additional modifications of attribute names in the rules
configuration are not required, so they must stay as is:

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

However, when using the `Nested` rule with multiple properties per rule set, special characters need to be escaped with
a backslash (`\`) for value paths to be correct and to be possible to reverse them back from string to individual
items. See the [Using keys containing separator / shortcut] section for more details.

This can be used as an alternative to using a custom separator.

#### Filtering by a specific property

This list can be also filtered by a specific property. Only top-level properties are supported.

```php
use Yiisoft\Validator\Result;

/** @var Result $result */
$result->getPropertyErrorMessagesIndexedByPath('user');
```

The output for example above:

```php
[
    'firstName' => ['Value cannot be blank.'],
    'lastName' => ['This value must contain at least 4 characters.'],
];
```

## Error objects list

When even these representations are not enough, an initial unmodified list of error objects can be accessed via
this method:

```php
use Yiisoft\Validator\Result;

/** @var Result */
$result->getErrors();
```

Each error stores the following data:

- Message. Either a simple message like `This value is wrong.` or a template with placeholders enclosed in curly braces
  (`{}`), for example: `Value must be no less than {min}.`. The actual formatting is done in `Validator` depending on
  the configured translator.
- Template parameters for substitution during formatting, for example: `['min' => 7]`.
- A path to a value within a checked data structure, for example: `['user', 'name', 'firstName']`.

### An example of an application

What this can be useful for? For example, to build a nested tree of error messages indexed by property names:

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

It's intentionally not provided out of the box due to the complexity of iteration. However, this can be useful for dumping
as JSON and storing in logs for example.

Debugging original error objects is also more convenient.

### Filtering by a specific property

This list can be also filtered by a specific property. Only top-level attributes are supported.

```php
use Yiisoft\Validator\Result;

/** @var Result $result */
$result->getPropertyErrors('email');
```

[Using keys containing separator / shortcut]: built-in-rules-nested.md#using-keys-containing-separator--shortcut
[will cast keys to the int type]: https://www.php.net/manual/en/language.types.array.php
