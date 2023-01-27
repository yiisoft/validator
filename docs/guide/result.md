# Result

The validation result is an object containing errors' data that occurred during validation.

## Is validation successful?

To just check the status of validation (whether a data is valid as a whole), use the following `Result` API call:

```php
use Yiisoft\Validator\Result;

/** @var Result */
$result->isValid();
```

It can be narrowed down to a specific attribute:

```php
use Yiisoft\Validator\Result;

/** @var Result */
$result->isAttributeValid('name');
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

An example of output with `age` and `email` attributes:

```php
[
    'Value must be no less than 21.',
    'This value is not a valid email address.',
    'A custom error message.',
];
```

It's easy to display and iterate, however, with a bigger amount of attributes and depending on a message, it can be
problematic to understand which attribute an error belongs to.

##### Error messages not bound to a specific attribute

Sometimes error messages are not related to a specific attribute. It can happen during the validation of
multiple attributes depending on each other for example. Use the following `Result` API call:

```php
$result->getCommonErrorMessages();
```

The output for example above:

```php
[
    'A custom error message.',
];
```

##### Filtering by a specific attribute

This list can be also filtered by a specific attribute. Only top-level attributes are supported.

```php
$result->getAttributeErrorMessages('email');
```

The output for example above:

```php
[
    'This value is not a valid email address.',
];
```

### Error messages indexed by attribute

To group error messages by attribute, use the following `Result` API call:

```php
use Yiisoft\Validator\Result;

/** @var Result */
$result->getErrorMessagesIndexedByAttribute();
```

An example of output:

```php
[
    'name' => [
        'Value cannot be blank.',
        'This value must contain at least 4 characters.',
    ],
    'email' => ['This value is not a valid email address.'],
    // Error messages not bound to specific attribute are grouped under empty string key.
    '' => ['A custom error message.'],
];
```

Note that the result is always a 2-dimensional array with attribute names as keys at the first nesting level. This means
that further nesting of attributes is not supported. Returning to the previous example, when `name` and `email` belong 
to a `user` attribute, the output will be:

```php
[
    'user' => [
        'Value cannot be blank.',
        'This value must contain at least 4 characters.',
        'This value is not a valid email address.'
    ],
    // Error messages not bound to specific attribute are grouped under empty string key.
    '' => ['A custom error message.'],
];
```

Also keep in mind that attribute names are always strings, even when used with `Each`:

```php
$rule = new Each([new Number(min: 21)]),
```

Given `[21, 22, 23, 20]` input, the output will be: 

```php
[
    '1' => ['Value must be no less than 21.'],
    '2' => ['Value must be no less than 21.'],
],
```

### Error messages indexed by path

This is probably the most advanced representation offered by built-in methods. The grouping is done by path - a 
concatenated attribute sequence showing the location of errored value within a data structure. A separator is customizable, 
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
    // Error messages not bound to specific attribute are grouped under empty string key.
    '' => ['A custom error message.'],
];
```

A path can contain integer elements too (when using the `Each` rule for example):

```php
[
    'charts.0.points.0.coordinates.y' => ['Value must be no greater than 10.'],
];
```

#### Resolving special characters collision in attribute names


When the attribute name contains a value path separator (dot - `.` by default) or `Each` rule shortcut (asterisk -`*`), 
they're automatically escaped using a backslash (`\​`) in the error messages list:

```php
[
    '\*country\.code' => ['Value cannot be blank.'],
],
```

In case of using a single attribute per rule set, any additional modifications of attribute names in the rules
configuration are not required, so they must stay as is:

```php
use Yiisoft\Validator\Rule\In;
use Yiisoft\Validator\Rule\Required;

$rules = [
    '*country.code' => [
        new Required();
        new In(['ru', 'en'], skipOnError: true),
    ],
];
```

However, when using the `Nested` rule with multiple attributes per rule set, special characters need to be escaped with 
a backslash (`\​`) for value paths to be correct and to be possible to reverse them back from string to individual 
items. See the [Using keys containing separator/shortcut] section for more details.

This can be used as an alternative to using a custom separator.

##### Filtering by a specific attribute

This list can be also filtered by a specific attribute. Only top-level attributes are supported.

```php
use Yiisoft\Validator\Result;

/** @var Result */
$result->getAttributeErrorMessagesIndexedByPath('user');
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

#### An example of an application

What this can be useful for? For example, to build a nested tree of error messages indexed by attribute names:

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

#### Filtering by a specific attribute

This list can be also filtered by a specific attribute. Only top-level attributes are supported.

```php
use Yiisoft\Validator\Result;

/** @var Result */
$result->getAttributeErrors('email');
```

[Using keys containing separator / shortcut]: #using-keys-containing-separator--shortcut
