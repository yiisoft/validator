# Conditional validation

Rules contain several options for skipping themselves in set under certain conditions. Not every rule supports all of 
these options, but the vast majority do.

## `skipOnError` - skip a rule in the set if the previous one failed

By default, if an error occurs while validating an attribute, all other rules in the set are processed. To
change this behavior, use `skipOnError: true` for rules that need to be skipped:

In the following example, the username length check is skipped if the username is not filled.

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

Note that this setting must be set for each rule that needs to be skipped on error.

The same effect can be achieved with `StopOnError` and `Composite` rules, which can be useful for a larger number of rules.

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

Missing/empty values of attributes will still be validated. If the value is missing, it is then assumed to be `null`.
If you want the attribute to be optional use `skipOnEmpty: true`.

An example with an optional language attribute:

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

If the attribute is required, it is more appropriate to use `skipOnError: true` instead of the `Required` rule.
This is because the detection of empty values within the `Required` rule and skipping in further rules
can be set separately. This is described in more detail below, see [Configuring empty criteria in other rules] section.

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

What is considered empty can vary depending on the scope of usage.

The value passed to `skipOnEmpty` is called "empty criteria". It is always a callback at the end, but shortcuts are also
supported due to normalization:

- When `false` or `null`, `Yiisoft\Validator\EmptyCriteria\NeverEmpty` is automatically used as a callback - every value 
is considered non-empty and validated without skipping (default).
- When `true`, `Yiisoft\Validator\EmptyCriteria\WhenEmpty` is automatically used as a callback - only passed
(corresponding attribute must be present) and non-empty values (not `null`, `[]`, or `''`) are validated.
- If a custom callback is set, it is used to determine emptiness.

`false` is usually more suitable for HTML forms and `true` - for APIs.

There are some more criteria that have no shortcuts and need to be set explicitly because they are less used:

- `Yiisoft\Validator\EmptyCriteria\WhenMissing` - a value that is treated as empty only if it is missing (not passed at all).
- `Yiisoft\Validator\EmptyCriteria\WhenNull` - limits empty values to `null` only.

An example with using `WhenNull` as parameter (note that an instance is passed, not a class name):

```php
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\EmptyCriteria\WhenNull;

new Number(asInteger: true, max: 100, skipOnEmpty: new WhenNull());
```

### Custom empty criteria

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

Using the class has the benefit of the code reusability.

### Using the same non-default empty criteria for all the rules

For multiple rules, this can also be more conveniently set at the validator level:

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

### Configuring empty criteria in other rules

Some rules, such as `Required` can't be skipped for empty values - that would defeat the purpose of the rule.
However, empty criteria can be configured here for detecting when a value is empty. Note - this does not skip the rule.
It only determines what the empty condition is:

```php
use Yiisoft\Validator\Rule\Required;

$rule = new Required(
    emptyCriteria: static function (mixed $value, bool $isAttributeMissing): bool {
        return $isAttributeMissing || $value === '';
    },
);
```

It is also possible to set it globally for all rules of this type at the handler level via 
`RequiredHandler::$defaultEmptyCriteria`.

## `when`

`when` provides the option to apply the rule dependent on a condition. It determines if the rule will be skipped.
The function for `when` is as follows:

```php
function (mixed $value, ValidationContext $context): bool;
```

where:

- `$value` is a validated value;
- `$context` is a validation context;
- `true` as a return value means that the rule must be applied and a `false` means it must be skipped.


In this example the state is only required when the country is `Brazil`. `$context->getDataSet()->getAttributeValue()`
method also allows you to get any other attribute's value within the `ValidationContext`.

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

As an alternative to functions, callable classes can be used instead. This approach has the advantage of code reusability.
See the [Skip on empty] section for an example.

[Configuring empty criterias in other rules]: #configuring-empty-criterias-in-other-rules
[Skip on empty]: #skiponempty---skipping-a-rule-if-the-validated-value-is-empty
