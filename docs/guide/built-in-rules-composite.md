# `Composite` - grouping multiple validation rules

`Composite` allows to group multiple rules and configure the common [skipping options], such as `skipOnEmpty`, 
`skipOnError` and `when`, for the whole set only once instead of repeating them in each rule:

```php
use Yiisoft\Validator\Rule\Composite;
use Yiisoft\Validator\Rule\Email;
use Yiisoft\Validator\Rule\HasLength;

new Composite(
    [
        new HasLength(max: 255),
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
use Yiisoft\Validator\Rule\HasLength;
use Yiisoft\Validator\Rule\Regex;

final class UsernameRuleSet extends Composite
{
    public function getRules(): array
    {
        return [
            new HasLength(min: 2, max: 20),
            new Regex('~^[a-z_\-]*$~i'),
        ];
    }
}
```

And use it just like a single regular rule:

```php
use Yiisoft\Validator\Validator;

$result = (new Validator())->validate('John', new UsernameRuleSet());
````

It can also be applied to multiple attributes:

```php
use Yiisoft\Validator\Rule\Composite;
use Yiisoft\Validator\Rule\Number;

final class CoordinatesRuleSet extends Composite
{
    public function getRules(): array
    {
        return [
            'latitude' => new Number(min: -90, max: 90),
            'longitude' => new Number(min: -90, max: 90),
        ];
    }
}
```

Even the problem of reusing only a one rule with the same arguments can be solved with `Composite`:

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

[skipping options]: conditional-validation.md
