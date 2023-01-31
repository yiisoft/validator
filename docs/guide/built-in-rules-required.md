# `Required` - verifying that a value is provided

Use the `Required` rule to ensure that a value is provided (not empty).

By default, a value is considered empty only when it is either:

- Not passed at all.
- `null`.
- An empty string (after trimming).
- An empty array.

## Customizing empty condition

Which values are considered empty can be customized via the `$emptyCondition` option. Unlike in [skipOnEmpty],
no normalization is performed here, so only a callable or a special class is accepted. For more details see
the [Empty condition basics] section.

An example with custom empty condition that limits empty values to `null` only:

```php
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\EmptyCondition\WhenNull;

new Required(emptyCondition: new WhenNull());
```

It's also possible to set it globally for all rules of this type at the handler level via 
`RequiredHandler::$defaultEmptyCondition`.

## Usage with other rules

`Required` is rarely used by itself. When combining it with other rules, make sure it's placed first:

```php
use Yiisoft\Validator\Rule\HasLength;
use Yiisoft\Validator\Rule\Required;

$rules = [
    new Required(),
    new HasLength(min: 1, max: 50),
];
```

With these settings, `HasLength` will still run in the case of an empty value. To prevent this, set up a conditional
validation:

```php
use Yiisoft\Validator\Rule\HasLength;
use Yiisoft\Validator\Rule\Required;

$rules = [
    new Required(),
    new HasLength(min: 1, max: 50, skipOnError: true),
];
```

Other ways of configuring conditional validation are described in the [Conditional validation] section.

[skipOnEmpty]: conditional-validation.md#skiponempty---skipping-a-rule-if-the-validated-value-is-empty
[Empty condition basics]: conditional-validation.md#empty-condition-basics
[Conditional validation]: conditional-validation.md
