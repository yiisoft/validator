# `Required` - verifying that a value is provided

Use `Required` rule to make sure a value is provided (not empty).

By default, a value is considered empty only when it is either:

- Not passed at all.
- `null`.
- An empty string (after trimming).
- An empty array.

## Customizing empty criteria

What values are considered empty can be customized via `$emptyCriteria` option. Unlike in [skipOnEmpty], normalization 
is not performed here, so only a callable or a special class are accepted. For more details see [Empty criteria basics] 
section.

An example with custom empty criteria that limits empty values to `null` only:

```php
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\EmptyCriteria\WhenNull;

new Required(emptyCriteria: new WhenNull());
```

It's also possible to set it globally for all rules of this type at the handler level via 
`RequiredHandler::$defaultEmptyCriteria`.

## Using with other rules

`Required` is rarely used on its own. When combining with other rules, make sure it's placed first:

```php
use Yiisoft\Validator\Rule\HasLength;
use Yiisoft\Validator\Rule\Required;

$rules = [
    new Required(),
    new HasLength(min: 1, max: 50),
];
```

With these settings, in case of an empty value, `HasLength` will still run. In order to prevent that, set up conditional
validation:

```php
use Yiisoft\Validator\Rule\HasLength;
use Yiisoft\Validator\Rule\Required;

$rules = [
    new Required(),
    new HasLength(min: 1, max: 50, skipOnError: true),
];
```

The other ways of configuring conditional validation are described in [Conditional validation] section.

[skipOnEmpty]: conditional-validation.md#skiponempty---skipping-a-rule-if-the-validated-value-is-empty
[Empty criteria basics]: conditional-validation.md#empty-criteria-basics
[Conditional validation]: conditional-validation.md
