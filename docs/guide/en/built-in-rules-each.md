# `Each` - applying the same rules for each data item in the set

The `Each` rule allows the same rules to be applied to each data item in the set. The following example shows
the configuration for validating [RGB color](https://en.wikipedia.org/wiki/RGB_color_model) components:

```php
use Yiisoft\Validator\Rule\Each;
use Yiisoft\Validator\Rule\Integer;

new Each([
    new Integer(min: 0, max: 255),
]);
```

By combining with another built-in rule called `Count` we can be sure that the number of components is exactly 3:

```php
use Yiisoft\Validator\Rule\Count;
use Yiisoft\Validator\Rule\Each;
use Yiisoft\Validator\Rule\Integer;

$rules = [
    // Applies to a whole set.
    new Count(3),
    // Applies to individual set items.
    new Each(        
        // For single rules, wrapping with array / iterable is not necessary.
        new Integer(min: 0, max: 255),
    ),
];
```

## Stopping on first error

By default, `Each` validates all items in the set and collects all errors. To stop validation at the first item
that produces an error, use the `stopOnError` parameter:

```php
use Yiisoft\Validator\Rule\Each;
use Yiisoft\Validator\Rule\Integer;

new Each(
    rules: [new Integer(min: 0, max: 255)],
    stopOnError: true,
);
```

## Accessing the current key

During validation of each item, the current iteration key is available through the validation context
parameter `Each::PARAMETER_EACH_KEY`. This can be useful in `when` callbacks or custom rule handlers:

```php
use Yiisoft\Validator\Rule\Each;
use Yiisoft\Validator\ValidationContext;

// Inside a when callback or custom rule handler:
$currentKey = $context->getParameter(Each::PARAMETER_EACH_KEY);
```

## Using with `Nested`

Validated data items are not limited to only "simple" values — `Each` can be used both within a `Nested` and contain
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
                    'rgb' => [
                        new Count(3),
                        new Each([new Number(min: 0, max: 255)]),
                    ],
                ]),
            ]),
        ]),
    ]),
]);
```

For more information about using it with `Nested`, see the [Nested](built-in-rules-nested.md) guide.
