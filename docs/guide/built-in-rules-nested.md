## `Nested` - nested and related data

### Basic usage

In many cases there is a need to validate related data in addition to current entity / model. There is a `Nested` rule
for this purpose:

```php
use Yiisoft\Validator\ValidatorInterface;
use Yiisoft\Validator\Rule\HasLength;
use Yiisoft\Validator\Rule\Nested;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Rule\Required;

// Usually obtained from container
$validator = $container->get(ValidatorInterface::class);

$data = ['author' => ['name' => 'Alexey', 'age' => '31']];
$rule = new Nested([
    'title' => [new Required()],
    'author' => new Nested([
        'name' => [new HasLength(min: 3)],
        'age' => [new Number(min: 18)],
    )];
]);
$errors = $validator->validate($data, [$rule])->getErrorMessagesIndexedByPath();
```

### Other configuration options

A dot notation can be used as an alternative way of configuration. In this case the example above will be presented as
following:

```php
use Yiisoft\Validator\Rule\HasLength;
use Yiisoft\Validator\Rule\Nested;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Rule\Required;

$rule = new Nested([
    'title' => [new Required()],
    'author.name' => [new HasLength(min: 3)],
    'author.age' => [new Number(min: 18)],
)];
```

It's also possible to combine both of these approaches:

```php
use Yiisoft\Validator\Rule\HasLength;
use Yiisoft\Validator\Rule\Nested;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Rule\Required;

$data = ['author' => ['name' => 'Alexey', 'age' => '31']];
$rule = new Nested([
    'author' => new Nested([
        'name' => [new HasLength(min: 3)],
        'age' => [new Number(min: 18)],
    )];
]);
```

### Advanced usage

A more complex real-life example is a chart that is made of points. This data is represented as arrays. `Nested` can be
combined with `Each` rule to validate such similar structures:

```php
use Yiisoft\Validator\ValidatorInterface;
use Yiisoft\Validator\Rule\Count;
use Yiisoft\Validator\Rule\Each;
use Yiisoft\Validator\Rule\Nested;

// Usually obtained from container
$validator = $container->get(ValidatorInterface::class);

$data = [
    'charts' => [
        [
            'points' => [
                ['coordinates' => ['x' => -11, 'y' => 11], 'rgb' => [-1, 256, 0]],
                ['coordinates' => ['x' => -12, 'y' => 12], 'rgb' => [0, -2, 257]]
            ],
        ],
        [
            'points' => [
                ['coordinates' => ['x' => -1, 'y' => 1], 'rgb' => [0, 0, 0]],
                ['coordinates' => ['x' => -2, 'y' => 2], 'rgb' => [255, 255, 255]],
            ],
        ],
        [
            'points' => [
                ['coordinates' => ['x' => -13, 'y' => 13], 'rgb' => [-3, 258, 0]],
                ['coordinates' => ['x' => -14, 'y' => 14], 'rgb' => [0, -4, 259]],
            ],
        ],
    ],
];
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
                        new Count(exactly: 3);
                        new Number(min: 0, max: 255),
                    ]),
                ]),
            ]),
        ]),
    ]),
]);
$errors = $rule->validate($data, [$rule])->getErrorMessagesIndexedByPath();
```

The contents of the errors will be:

```php
$errors = [
    'charts.0.points.0.coordinates.x' => ['Value must be no less than -10.'],
    // ...
    'charts.0.points.0.rgb.0' => ['Value must be no less than 0. -1 given.'],
    // ...
];
```

#### Using shortcut

A shortcut can be used to simplify `Nested` and `Each` combinations:

```php
use Yiisoft\Validator\Rule\Count;
use Yiisoft\Validator\Rule\Each;
use Yiisoft\Validator\Rule\Nested;

$rule = new Nested([
    'charts.*.points.*.coordinates.x' => [new Number(min: -10, max: 10)],
    'charts.*.points.*.coordinates.y' => [new Number(min: -10, max: 10)],
    'charts.*.points.*.rgb' => [
        new Count(exactly: 3);
        new Number(min: 0, max: 255),
    ]),
]);
```

With additional grouping it can also be rewritten like this:

```php
use Yiisoft\Validator\Rule\Count;
use Yiisoft\Validator\Rule\Each;
use Yiisoft\Validator\Rule\Nested;

$rule = new Nested([
    'charts.*.points.*.coordinates' => new Nested([
        'x' => [new Number(min: -10, max: 10)],
        'y' => [new Number(min: -10, max: 10)],
    ]),
    'charts.*.points.*.rgb' => [
        new Count(exactly: 3);
        new Number(min: 0, max: 255),
    ]),
]);
```

This is less verbose, but the downside of this approach is that you can't additionally configure dynamically generated
`Nested` and `Each` pairs. If you need this, please refer to example provided in "Basic usage" section.

#### Using keys containing separator / shortcut

If a key contains the separator (`.`), it must be escaped with backslash (`\`) in rule config in order to work
correctly. In the input data escaping is not needed. Here is an example with two nested keys named `author.data` and
`name.surname`:

```php
use Yiisoft\Validator\Rule\Nested;

$rule = new Nested([
    'author\.data.name\.surname' => [
        new HasLength(min: 3),
    ],
]);
$data = [
    'author.data' => [
        'name.surname' => 'Dmitry',
    ],
];
```

Note that in case of using multiple nested rules for configuration escaping is still required:

```php
use Yiisoft\Validator\Rule\Nested;

$rule = new Nested([
    'author\.data' => new Nested([
        'name\.surname' => [
            new HasLength(min: 3),
        ],
    ]),
]);
$data = [
    'author.data' => [
        'name.surname' => 'Dmitriy',
    ],
];
```

The same applies to shortcut:

```php
use Yiisoft\Validator\Rule\Nested;

$rule = new Nested([
    'charts\.list.*.points\*list.*.coordinates\.data.x' => [
        // ...
    ],
    'charts\.list.*.points\*list.*.coordinates\.data.y' => [
        // ...
    ],
    'charts\.list.*.points\*list.*.rgb' => [
        // ...
    ],
]);
$data = [
    'charts.list' => [
        [
            'points*list' => [
                [
                    'coordinates.data' => ['x' => -11, 'y' => 11], 'rgb' => [-1, 256, 0],
                ],
            ],
        ],
    ],
];
```
