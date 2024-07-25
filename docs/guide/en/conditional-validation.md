# Conditional validation

Rules contain several options for skipping themselves under certain conditions. Not every rule supports all of 
these options, but the vast majority do.

## `skipOnError` - skip a rule in the set if the previous one failed

By default, if an error occurs while validating a property, all further rules in the set are processed. To
change this behavior, use `skipOnError: true` for rules that need to be skipped:

In the following example, checking the length of a username is skipped if the username is not filled.

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
        new Required(),
        // Skipped because "name" is required but not filled.
        new Length(min: 4, max: 20, skipOnError: true),
        // Validated because "skipOnError" is "false" by default. Set to "true" to skip it as well.
        new Regex('^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$'),
    ],
    'age' => [
        // Validated because "age" is a different property with its own set of rules.
        new Required(),
        // Validated because "skipOnError" is "false" by default. Set to "true" to skip it as well.
        new Number(min: 21),
    ],
];
$result = (new Validator())->validate($data, $rules);
```

Note that this setting must be set for each rule that needs to be skipped on error.

The same effect can be achieved with `StopOnError` and `Composite` rules, which can be more convenient for a larger
number of rules.

Using `StopOnError`:

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

By default, missing/empty values of properties are validated. If the value is missing, it is assumed to be `null`.
If you want the property to be optional, use `skipOnEmpty: true`.

An example with an optional language property:

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

If the property is required, it is more appropriate to use `skipOnError: true` along with the preceding `Required` rule
instead of `skipOnEmpty: true`. This is because the detection of empty values within the `Required` rule and skipping
in further rules can be set separately. This is described in more detail below,
see [Configuring empty condition in other rules] section.

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

What is considered empty can vary depending on the scope of usage.

The value passed to `skipOnEmpty` is called "empty condition". Due to normalization the following shortcut values are
supported:

- When `false` or `null`, `Yiisoft\Validator\EmptyCondition\NeverEmpty` is used automatically as a callback - every value 
is considered non-empty and validated without skipping (default).
- When `true`, `Yiisoft\Validator\EmptyCondition\WhenEmpty` is used automatically as a callback - only passed
(corresponding property must be present) and non-empty values (not `null`, `[]`, or `''`) are validated.
- If a custom callback is set, it is used to determine emptiness.

`false` is usually more suitable for HTML forms and `true` for APIs.

There are some more conditions that have no shortcuts and need to be set explicitly because they are less used:

- `Yiisoft\Validator\EmptyCondition\WhenMissing` - a value is treated as empty only when it is missing (not passed at all).
- `Yiisoft\Validator\EmptyCondition\WhenNull` - limits empty values to `null` only.

An example with using `WhenNull` as parameter (note that an instance is passed, not a class name):

```php
use Yiisoft\Validator\Rule\Integer;
use Yiisoft\Validator\EmptyCondition\WhenNull;

new Integer(max: 100, skipOnEmpty: new WhenNull());
```

### Custom empty condition

For even more customization you can use your own class that implements the `__invoke()` magic method. Here is an example 
where a value is empty only if it is missing (when using properties) or equals exactly to zero.

```php
use Yiisoft\Validator\Rule\Number;

final class WhenZero
{
    public function __invoke(mixed $value, bool $isPropertyMissing): bool
    {
        return $isPropertyMissing || $value === 0;
    }
}

new Integer(max: 100, skipOnEmpty: new WhenZero());
```

or just a callable:

```php
use Yiisoft\Validator\Rule\Integer;

new Integer(
    max: 100,
    skipOnEmpty: static function (mixed $value, bool $isPropertyMissing): bool {
        return $isPropertyMissing || $value === 0;
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
    skipOnEmpty: static function (mixed $value, bool $isPropertyMissing): bool {
        return $value === 0;
    }
);
```

### Configuring empty condition in other rules

Some rules, such as `Required` can't be skipped for empty values - that would defeat the purpose of the rule.
However, empty condition can be configured here for detecting when a value is empty. Note - this does not skip the rule.
It only determines what the empty condition is:

```php
use Yiisoft\Validator\Rule\Required;

$rule = new Required(
    emptyCondition: static function (mixed $value, bool $isPropertyMissing): bool {
        return $isPropertyMissing || $value === '';
    },
);
```

It is also possible to set it globally for all rules of this type at the handler level via 
`RequiredHandler::$defaultEmptyCondition`.

## `when`

`when` provides the option to apply the rule depending on a condition of the provided callable. A callable's result
determines if the rule will be skipped. The signature of the function is the following:

```php
function (mixed $value, ValidationContext $context): bool;
```

where:

- `$value` is a validated value;
- `$context` is a validation context;
- `true` as a returned value means that the rule must be applied and a `false` means it must be skipped.

In this example the state is only required when the country is `Brazil`. `$context->getDataSet()->getPropertyValue()`
method allows you to get any other property's value within the `ValidationContext`.

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
            return $context->getDataSet()->getPropertyValue('country') === 'Brazil';
        },
    )
];
$result = (new Validator())->validate($data, $rules);
```

As an alternative to functions, callable classes can be used instead. This approach has the advantage of code reusability.
See the [Skip on empty] section for an example.

[Configuring empty condition in other rules]: #configuring-empty-condition-in-other-rules
[Skip on empty]: #skiponempty---skipping-a-rule-if-the-validated-value-is-empty
