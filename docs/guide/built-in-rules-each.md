# Each — applying the same rules for each data item in the set

`Each` rule allows to apply the same rules for each data item in the set. The following example demonstrates configation
for validation [RGB color] components:

```php
use Yiisoft\Validator\Rule\Each;
use Yiisoft\Validator\Rule\Number;

new Each([
    new Number(integerOnly: true, min: 0, max: 255),
]);

```

By combining with another built-in rule called `Count` we can be sure that the number of components is exactly 3:

```php
use Yiisoft\Validator\Rule\Count;
use Yiisoft\Validator\Rule\Each;
use Yiisoft\Validator\Rule\Number;

$rules = [
    // Applies to a whole set.
    new Count(exactly: 3),
    // Applies to individual set items.
    new Each(        
        // For single rules, wrapping with array / iterable is not necessary.
        new Number(integerOnly: true, min: 0, max: 255),
    ),
];
```

Validated data items are not limited to only "simple" values - `Each` can be used both within a `Nested` and contain 
`Nested` rule covering one-to-many and many-to-many relations:

```php
use Yiisoft\Validator\Rule\Count;
use Yiisoft\Validator\Rule\Each;
use Yiisoft\Validator\Rule\Nested;
use Yiisoft\Validator\Rule\Number;

$rule = new Nested([
    'charts' => new Each([
        new Nested([
            'points' => new Each([
                new Nested([
                    'coordinates' => new Nested([
                        'x' => [new Number(min: -10, max: 10)],
                        'y' => [new Number(min: -10, max: 10)],
                    ]),
                    'rgb' => new Each([
                        new Count(exactly: 3),
                        new Number(min: 0, max: 255),
                    ]),
                ]),
            ]),
        ]),
    ]),
]);
```

For more information about using with `Nested`, please refer to [Nested] guide.

[RGB color]: https://en.wikipedia.org/wiki/RGB_color_model
[Nested]: built-in-rules-nested.md
