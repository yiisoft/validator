# `Composite` - grouping multiple validation rules

`Composite` allows to group multiple rules and configure the common [skipping options](conditional-validation.md),
such as `skipOnEmpty`, `skipOnError` and `when`, for the whole set only once instead of repeating them in each rule:

```php
use Yiisoft\Validator\Rule\Composite;
use Yiisoft\Validator\Rule\Email;
use Yiisoft\Validator\Rule\Length;

new Composite(
    [
        new Length(max: 255),
        new Email(),
    ],
    skipOnEmpty: true,
);
```

## Reusing multiple rules / single rule with the same options

`Composite` is one of the few built-in rules that is not `final`. This means that you can extend it and override the
`getRules()` method to create a reusable set of rules:

```php
use Yiisoft\Validator\Rule\Composite;
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\Rule\Regex;

final class UsernameRuleSet extends Composite
{
    public function getRules(): array
    {
        return [
            new Length(min: 2, max: 20),
            new Regex('~^[a-z_\-]*$~i'),
        ];
    }
}
```

And use it just like a single regular rule:

```php
use Yiisoft\Validator\Validator;

$result = (new Validator())->validate('John', new UsernameRuleSet());
```

It can also be combined with [Nested](built-in-rules-nested.md) rule to reuse rules for multiple properties:

```php
use Yiisoft\Validator\Rule\Composite;
use Yiisoft\Validator\Rule\Nested;
use Yiisoft\Validator\Rule\Number;

final class CoordinatesRuleSet extends Composite
{
    public function getRules(): array
    {
        return [
            new Nested(
                'latitude' => new Number(min: -90, max: 90),
                'longitude' => new Number(min: -90, max: 90),
            ),
        ];
    }
}
```

Even the problem of reusing only one rule with the same arguments can be solved with `Composite`:

```php
use Yiisoft\Validator\Rule\Composite;
use Yiisoft\Validator\Rule\Number;

final class ChartCoordinateRuleSet extends Composite
{
    public function getRules(): array
    {
        return [new Number(min: -10, max: 10)];
    }
}
```
