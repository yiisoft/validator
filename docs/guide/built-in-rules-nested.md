# `Nested` - validation of nested and related data

## Basic usage (one-to-one relation)

In many cases there is a need to validate related data in addition to the current entity / model.
There is a `Nested` rule for this purpose. 

```php
use Yiisoft\Validator\Rule\HasLength;
use Yiisoft\Validator\Rule\Nested;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\Validator;

$data = ['author' => ['name' => 'John', 'age' => '31']];
$rule = new Nested([
    'title' => [new Required()],
    'author' => new Nested([
        'name' => [new HasLength(min: 3)],
        'age' => [new Number(min: 18)],
    ]),
]);
$errors = (new Validator())->validate($data, $rule)->getErrorMessagesIndexedByPath();
```

The output of `$errors` will be:

```php
[
    'title' => ['Value cannot be blank.'], 
    'author.age' => ['Value must be no less than 18.'],
];
```
In this example, an additional instance of the `Nested` rule is used for each relation. The other types of configuration
are possible, they are described below.

Other representations of the error list are covered in the [Result] section. 

## Other configuration options

### Dot notation

Dot notation can be used as an alternative method of configuration. In this case, the above example is represented as
follows:

```php
use Yiisoft\Validator\Rule\HasLength;
use Yiisoft\Validator\Rule\Nested;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Rule\Required;

$rule = new Nested([
    'title' => [new Required()],
    'author.name' => [new HasLength(min: 3)],
    'author.age' => [new Number(min: 18)],
]);
```

It's also possible to combine both approaches:

```php
use Yiisoft\Validator\Rule\HasLength;
use Yiisoft\Validator\Rule\Nested;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Rule\Required;

$data = ['author' => ['name' => 'Alexey', 'age' => '31']];
$rule = new Nested([
    'content' => new Nested([
        'title' => [new Required()],
        'description' => [new Required()],
    ]),
    'author.name' => [new HasLength(min: 3)],
    'author.age' => [new Number(min: 18)],
]);
```

### Omitting code

Some code parts can be omitted for brevity.

#### Inner `Nested` instances

Inner `Nested` instances can be omitted, but only for nesting levels no greater than 2:

```php
use Yiisoft\Validator\Rule\HasLength;
use Yiisoft\Validator\Rule\Nested;

$rule = new Nested([
    'author' => [
        'name' => [new HasLength(min: 1)],
    ],
]);
```

This will not work:

```php
use Yiisoft\Validator\Rule\HasLength;
use Yiisoft\Validator\Rule\Nested;

$rule = new Nested([
    'author' => [
        'name' => [
            'surname' => [new HasLength(min: 1)],
        ],
    ],
]);
```

This limitation is planned to be removed in the future, but for now, in order for this example to work, it needs to be 
rewritten to be wrapped with another `Nested` instance or the short syntax shown above.

#### Inner arrays for single rules

Inner arrays for single rules can be omitted regardless of nesting level:

```php
use Yiisoft\Validator\Rule\HasLength;
use Yiisoft\Validator\Rule\Nested;

$rule = new Nested([
    'author' => [
        'name' => [
            'surname' => new HasLength(min: 1),
        ],
    ],
]);
```

## Advanced usage

### One-to-many and many-to-many relations

The example in the [Basic usage] section shows how to work with one-to-one relations only, when the `Nested' rule is sufficient for
referencing relations. But it can be combined with other complex rules, such as `Each', to validate one-to-many and
many-to-many relations as well:

```php
use Yiisoft\Validator\Rule\Count;
use Yiisoft\Validator\Rule\Each;
use Yiisoft\Validator\Rule\Nested;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Validator;

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
                        new Count(exactly: 3),
                        new Number(min: 0, max: 255),
                    ]),
                ]),
            ]),
        ]),
    ]),
]);
$result = (new Validator())->validate($data, $rule);
$errors = $result->getErrorMessagesIndexedByPath();
```

The content of the errors will be:

```php
$errors = [
    'charts.0.points.0.coordinates.x' => ['Value must be no less than -10.'],
    // ...
    'charts.0.points.0.rgb.0' => ['Value must be no less than 0. -1 given.'],
    // ...
];
```

### Using shortcut

A shortcut can be used to simplify `Nested` and `Each` combinations:

```php
use Yiisoft\Validator\Rule\Count;
use Yiisoft\Validator\Rule\Nested;
use Yiisoft\Validator\Rule\Number;

$rule = new Nested([
    'charts.*.points.*.coordinates.x' => [new Number(min: -10, max: 10)],
    'charts.*.points.*.coordinates.y' => [new Number(min: -10, max: 10)],
    'charts.*.points.*.rgb' => [
        new Count(exactly: 3),
        new Number(min: 0, max: 255),
    ],
]);
```

With additional grouping, it can also be rewritten like this:

```php
use Yiisoft\Validator\Rule\Count;
use Yiisoft\Validator\Rule\Nested;
use Yiisoft\Validator\Rule\Number;

$rule = new Nested([
    'charts.*.points.*.coordinates' => new Nested([
        'x' => [new Number(min: -10, max: 10)],
        'y' => [new Number(min: -10, max: 10)],
    ]),
    'charts.*.points.*.rgb' => [
        new Count(exactly: 3),
        new Number(min: 0, max: 255),
    ],
]);
```

This is less verbose, but the downside of this approach is that you can't additionally configure dynamically generated
`Nested` and `Each` pairs. If you need to do this, use the explicit form of configuration (see the example provided in 
[Basic usage] section).

### Using PHP attributes

Rules and relations can be declared through DTO with PHP attributes: 

```php
use Yiisoft\Validator\Rule\Count;
use Yiisoft\Validator\Rule\Each;
use Yiisoft\Validator\Rule\Nested;
use Yiisoft\Validator\Rule\Number;

final class ChartSet
{
    public function __construct(
        #[Each([new Nested(Chart::class)])]
        private array $charts,
    ) {
    }
}

final class Chart
{
    public function __construct(
        #[Each([new Nested(Point::class)])]
        private array $points,
    ) {
    }
}

final class Point
{
    public function __construct(
        #[Nested(Coordinates::class)]
        private Coordinates $coordinates,
        #[Count(exactly: 3)]
        #[Each([new Number(min: 0, max: 255)])]
        private array $rgb,
    ) {
    }
}

final class Coordinates
{
    public function __construct(
        #[Number(min: -10, max: 10)]
        private int $x,
        #[Number(min: -10, max: 10)]
        private int $y,
    ) {
    }
}
```

With the data in the associative array from the previous examples, we can use the class just to fetch the rules:

```php
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
$result = $validator->validate($data, ChartSet::class);
```

Or provide data along with rules in the same objects:

```php
$chartSet = new ChartSet(
    charts: [
        new Chart(
            points: [
                new Point(
                    coordinates: new Coordinates(x: -11, y: 11),
                    rgb: [-1, 256, 0],
                ),
                new Point(
                    coordinates: new Coordinates(x: -12, y: 12),
                    rgb: [0, -2, 257],
                ),
            ],       
        ),
        new Chart(
            points: [
                new Point(
                    coordinates: new Coordinates(x: -1, y: 1),
                    rgb: [0, 0, 0],
                ),
                new Point(
                    coordinates: new Coordinates(x: -2, y: 2),
                    rgb: [255, 255, 255],
                ),
            ],       
        ),
        new Chart(
            points: [
                new Point(
                    coordinates: new Coordinates(x: -13, y: 13),
                    rgb: [-3, 258, 0],
                ),
                new Point(
                    coordinates: new Coordinates(x: -14, y: 14),
                    rgb: [0, -4, 259],
                ),
            ],       
        ),
    ],
);
$result = $validator->validate($chartSet); // Note `$rules` argument is `null` here.
$errors = $result->getErrorMessagesIndexedByPath();
```

- - For more information about the different ways to configure rules using PHP attributes, see [Configuring rules via PHP
attributes] section. 
- For more information about possible data / rules combinations passed for validation, refer to the [Using validator] 
section. 

### Using keys containing separator / shortcut

If a key contains the separator (`.`), it must be escaped with backslash (`\`) in the rule config to work correctly.
So escaping is not needed in the input data. Here is an example with 2 nested keys named `author.data` and
`name.surname`:

```php
use Yiisoft\Validator\Rule\HasLength;
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

Note that escaping is still required when using multiple nested rules for configuration:

```php
use Yiisoft\Validator\Rule\HasLength;
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

The same is true for the `Each` shortcut:

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

[Result]: result.md
[Basic usage]: #basic-usage-one-to-one-relation
[Configuring rules via PHP attributes]: configuring-rules-via-php-attributes.md
[Using validator]: using-validator.md
