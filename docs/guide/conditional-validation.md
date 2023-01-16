# Conditional validation

Rules contain several options for skipping themselves in set under certain conditions. Not every rule supports all of 
these options, but the vast majority does.

## `skipOnError` - skipping a rule in the set if previous one errored

By default, if an error occurred during validation of an attribute, all further rules in this set are processed. To
change this behavior, use `skipOnError: true` for rules that need to be skipped:

In the following example checking the length of a username is skipped when it's not filled.

```php
use Yiisoft\Validator\Rule\HasLength;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Rule\Regex;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\Validator;

$data = [];
$rules = [
    'name' => [
        // Validated.
        new Required(),
        // Skipped because "name" is required but not filled.
        new HasLength(min: 4, max: 20, skipOnError: true),
        // Validated because "skipOnError" is "false" by default. Set to "true" to skip it as well.
        new Regex('^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$'),
    ],
    'age' => [
        // Validated because "age" is a different attribute with its own set of rules.
        new Required(),
        // Validated because "skipOnError" is "false" by default. Set to "true" to skip it as well.
        new Number(min: 21),
    ],
];
$result = (new Validator())->validate($data, $rules);
```

Note that this setting must be set for every rule that needs to be skipped on error.

The same effect can be achieved with `StopOnError` and `Composite` rules, which can be more convenient for a bigger 
amount of rules.

Using `StopOnError`:

```php
use Yiisoft\Validator\Rule\HasLength;
use Yiisoft\Validator\Rule\Regex;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\Rule\StopOnError;
use Yiisoft\Validator\Validator;

$data = [];
$rules = [
    'name' => new StopOnError([
        new Required(),
        new HasLength(min: 4, max: 20),
        new Regex('^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$'),
    ]),
];
$result = (new Validator())->validate($data, $rules);
```

Using `Composite`:

```php
use Yiisoft\Validator\Rule\Composite;
use Yiisoft\Validator\Rule\HasLength;
use Yiisoft\Validator\Rule\Regex;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\Validator;

$data = [];
$rules = [
    'name' => [
        new Required(),
        new Composite(
            [
                new HasLength(min: 4, max: 20),
                new Regex('^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$'),
            ],
            skipOnError: true,
        )
    ],
];
$result = (new Validator())->validate($data, $rules);
```

## `skipOnEmpty` - skipping a rule if the validated value is "empty"

By default, missing (when using attributes) and empty values are validated (if the value is missing, it's considered 
`null`). That is undesirable if you need an attribute to be optional. To change this behavior, use `skipOnEmpty: true`.

An example with optional language attribute:

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

If the attribute is required, it's more appropriate to use `skipOnError: true` instead with preceding `Required` rule. 
This is because empty values' detection within `Required` rule and for skipping in further rules can be set separately 
(this is described below in more detail, see "Configuring criterias in other rules" section).

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

### Empty criteria basics

What exactly to consider to be empty can vary depending on a scope of usage.

The value passed to `skipOnEmpty` is called "empty criteria". At the end it's always a callback, but shorcuts are 
supported too because of normalization:

- When `false` or `null`, `Yiisoft\Validator\EmptyCriteria\NeverEmpty` is used automatically as a callback - every value 
is considered non-empty and validated without skipping (default).
- When `true`, `Yiisoft\Validator\EmptyCriteria\WhenEmpty` is used automatically as a callback - only passed
(corresponding attribute must be present) and non-empty values (not `null`, `[]`, or `''`) are validated.
- If a custom callback  is set, it's used to determine emptiness.

`false` is usually more suitable for HTML forms and `true` - for APIs.

There are some more criterias that do not have shorcuts and need to be set explicitly because they are less used:

- `Yiisoft\Validator\EmptyCriteria\WhenMissing` - a value treated as empty only when it's missing (not passed at all).
- `Yiisoft\Validator\EmptyCriteria\WhenNull` - limits empty values to `null` only.

An example with using `WhenNull` as parameter (note that instance is passed, not a class name):

```php
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\EmptyCriteria\WhenNull;

new Number(asInteger: true, max: 100, skipOnEmpty: new WhenNull());
```

### Custom empty criteria

For even more customization you can use your own class implementing `__invoke()` magic method. Here is an example when
a value is empty only when it's missing (when using attributes) or equals exactly to zero.

```php
use Yiisoft\Validator\Rule\Number;

final class WhenZero
{
    public function __invoke(mixed $value, bool $isAttributeMissing): bool
    {
        return $isAttributeMissing || $value === 0;
    }
}

new Number(asInteger: true, max: 100, skipOnEmpty: new WhenZero());
```

or just a callable:

```php
use Yiisoft\Validator\Rule\Number;

new Number(
    asInteger: true, 
    max: 100, 
    skipOnEmpty: static function (mixed $value, bool $isAttributeMissing): bool {
        return $isAttributeMissing || $value === 0;
    }
);
```

Using the class has a benefit of the code reuse possibility.

### Using the same non-default empty criteria for all the rules

For multiple rules this can be also set more conveniently at the validator level:

```php
use Yiisoft\Validator\RuleHandlerResolver\SimpleRuleHandlerContainer;
use Yiisoft\Validator\Validator;

$validator = new Validator(skipOnEmpty: true); // Using the shortcut.
$validator = new Validator(
    new SimpleRuleHandlerContainer(),
    // Using the custom callback.
    skipOnEmpty: static function (mixed $value, bool $isAttributeMissing): bool {
        return $value === 0;
    }
);
```

### Configuring empty criterias in other rules

Some rules, like `Required` can't be skipped on empty values - that would violate the whole purpose of it. However, the 
empty criteria can be configured here too - not for skipping, but for detecting an empty value:

```php
use Yiisoft\Validator\Rule\Required;

$rule = new Required(
    emptyCriteria: static function (mixed $value, bool $isAttributeMissing): bool {
        return $isAttributeMissing || $value === '';
    },
);
```

It's also possible to set it globally for all rules at the handler level via `RequiredHandler::$defaultEmptyCriteria`.

## `when`

`when` provides an option to set a callable with an arbitrary condition determining whether a rule it was attached to
must be skipped. The signature of the function is like the following:

```php
function (mixed $value, ValidationContext $context): bool;
```

where:

- `$value` is validated value;
- `$context` is a validation context;
- `true` as returned value  means that rule must be applied and `false` - must be skipped.

In this example the state will be required only for `Brazil` counry. `$context->getDataSet()->getAttributeValue()` 
method allows to get any other attribute's value within a validated data set.

```php
use Yiisoft\Validator\Rule\HasLength;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\ValidationContext;
use Yiisoft\Validator\Validator;

$data = [];
$rules = [
    'country' => [
        new Required(),
        new HasLength(min: 2),
    ],
    'state' => new Required(
        when: static function (mixed $value, ValidationContext $context): bool {
            return $context->getDataSet()->getAttributeValue('country') === 'Brazil';
        },
    )
];
$result = (new Validator())->validate($data, $rules);
```

As an alternative for functions, callable classes can be used instead. This approach has a benefit of code reuse 
possibility. Please refer to "Skip on empty" section to see an example.
