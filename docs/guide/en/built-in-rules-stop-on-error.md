# `StopOnError` - stop validation on the first error

This rule applies to a group of rules and allows you to stop the validation for the whole group immediately after
an error occurs in any of the rules. This means that all rules following the rule that failed validation won't be
run at all.

This can be useful for performance-intensive validations, such as database queries or some complex calculations. 
The order of rules within a group is crucial here - the "lightweight" rules need to be placed above the "heavy" ones:

```php
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\Rule\StopOnError;
use Yiisoft\Validator\Validator;

$data = 2;
$rule = new StopOnError([
    new Length(min: 3), // "Lightweight" rule, will be run and won't pass the validation.
    new MyHeavyRule(), // "Heavy" rule, won't be run at all because of the existing error.
]);
$result = (new Validator())->validate($data, $rule);
```

When using with other rules and conditional validation, it behaves like a single unit. For example, with 
default settings it will not be skipped if the previous rule didn't pass the validation. To change this behavior, set 
`$skipOnError` to `true`. This allows to use it for limiting the list of errors per attribute to just the first one (in 
HTML forms, for example).

```php
$rules = [
    'attribute1' => new SimpleRule1(), // Let's say there is an error.
    // Then this rule is skipped completely with all its related rules because `skipOnError` is set to `true`. Useful
    // when all rules within `StopOnError` are heavy.
    'attribute2' => new StopOnError(
        [
            new HeavyRule1(), // Skipped.
            new HeavyRule2(), // Skipped.
        ],
        skipOnError: true,
    ),
    // This rule is not skipped because `skipOnError` is `false` by default. Useful for forcing validation and
    // limiting the errors.
    'attribute3' => new StopOnError([
        new SimpleRule2(), // Assuming there is another error.
        new SimpleRule3(), // Skipped.
    ]),
    // Skipping of other intermediate rules depends on `skipOnError` option set in these intermediate rules.
    'attribute4' => new SimpleRule4(), // Not skipped, because `skipOnError` is `false` by default.
]);
```

Use grouping / ordering / `skipOnError` option to achieve the desired effect.
