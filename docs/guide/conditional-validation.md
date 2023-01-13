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
        // Validated because "age" is a different attribute with its own set of rules..
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

## `skipOnEmpty`

By default, missing and empty values are validated (if the value is missing, it's considered `null`). That is
undesirable if you need a field to be optional. To change this behavior, use `skipOnEmpty: true`.

Note that not every rule has this option, but only the ones that implement `Yiisoft\Validator\SkipOnEmptyInterface`. For
example, `Required` rule doesn't. For more details see "Requiring values" section.

```php
use Yiisoft\Validator\Rule\Number;

new Number(asInteger: true, max: 100, skipOnEmpty: true);
```

What exactly to consider to be empty is vague and can vary depending on a scope of usage.

`skipOnEmpty` value is normalized to callback automatically:

- If `skipOnEmpty` is `false` or `null`, `Yiisoft\Validator\EmptyCriteria\NeverEmpty` is used automatically as
  callback - every value is considered non-empty and validated without skipping (default).
- If `skipOnEmpty` is `true`, `Yiisoft\Validator\EmptyCriteria\WhenEmpty` is used automatically for callback -
  only passed and non-empty values (not `null`, `[]`, or `''`) are validated.
- If custom callback  is set, it's used to determine emptiness.

Using first option is usually good for HTML forms. The second one is more suitable for APIs.

The empty values can be also limited to `null` only:

```php
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\EmptyCriteria\WhenNull;

new Number(asInteger: true, max: 100, skipOnEmpty: new WhenNull());
```

For even more customization you can use your own class implementing `__invoke()` magic method:

```php
use Yiisoft\Validator\Rule\Number;

final class SkipOnZero
{
    public function __invoke(mixed $value, bool $isAttributeMissing): bool
    {
        return $value === 0;
    }
}

new Number(asInteger: true, max: 100, skipOnEmpty: new SkipOnZero());
```

or just a callable:

```php
use Yiisoft\Validator\Rule\Number;

new Number(
    asInteger: true, 
    max: 100, 
    skipOnEmpty: static function (mixed $value, bool $isAttributeMissing): bool {
        return $value === 0;
    }
);
```

For multiple rules this can be also set more conveniently at validator level:

```php
use Yiisoft\Validator\RuleHandlerResolver\SimpleRuleHandlerContainer;
use Yiisoft\Validator\Validator;

$validator = new Validator(new SimpleRuleHandlerContainer(), skipOnEmpty: true);
$validator = new Validator(
    new SimpleRuleHandlerContainer(),
    skipOnEmpty: static function (mixed $value, bool $isAttributeMissing): bool {
        return $value === 0;
    }
);
```

Using `$isAttributeMissing` parameter such as `$context` also allows to check if attribute is missing / present:

```php
$skipOnEmpty = static function (mixed $value, bool $isAttributeMissing): bool {
    return $isAttributeMissing || $value === '';
};
```

## `when`

In some cases there is a need to apply rule conditionally. It could be performed by using `when`:

```php
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\ValidationContext;

new Number(
    when: static function ($value, ValidationContext $context): bool {
        return $context->getDataSet()->getAttributeValue('country') === Country::USA;
    },
    asInteger: true, 
    min: 100
);
```
If callable returns `true` rule is applied, when the value returned is `false`, rule is skipped.
