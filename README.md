<p align="center">
    <a href="https://github.com/yiisoft" target="_blank">
        <img src="https://yiisoft.github.io/docs/images/yii_logo.svg" height="100px">
    </a>
    <h1 align="center">Yii Validator</h1>
    <br>
</p>

The package provides data validation capabilities.

[![Latest Stable Version](https://poser.pugx.org/yiisoft/validator/v/stable.png)](https://packagist.org/packages/yiisoft/validator)
[![Total Downloads](https://poser.pugx.org/yiisoft/validator/downloads.png)](https://packagist.org/packages/yiisoft/validator)
[![Build status](https://github.com/yiisoft/validator/workflows/build/badge.svg)](https://github.com/yiisoft/validator/actions?query=workflow%3Abuild)
[![Code Coverage](https://codecov.io/gh/yiisoft/validator/branch/master/graph/badge.svg)](https://codecov.io/gh/yiisoft/validator)
[![Mutation testing badge](https://img.shields.io/endpoint?style=flat&url=https%3A%2F%2Fbadge-api.stryker-mutator.io%2Fgithub.com%2Fyiisoft%2Fwidget%2Fmaster)](https://dashboard.stryker-mutator.io/reports/github.com/yiisoft/validator/master)
[![static analysis](https://github.com/yiisoft/validator/workflows/static%20analysis/badge.svg)](https://github.com/yiisoft/validator/actions?query=workflow%3A%22static+analysis%22)
[![type-coverage](https://shepherd.dev/github/yiisoft/validator/coverage.svg)](https://shepherd.dev/github/yiisoft/validator)

## Features

- Could be used with any object.
- Supports PHP 8 attributes.
- Skip further validation if an error occurred for the same field.
- Skip validation of empty value.
- Error message formatting.
- Conditional validation.
- Could pass context to validation rule.
- Common rules bundled.

## Requirements

- PHP 8.0 or higher.

## Installation

The package could be installed with composer:

```shell
composer require yiisoft/validator
```

## General usage

Library could be used in two ways: validating a single value and validating a set of data.

### Validating a single value

```php
use Yiisoft\Validator\ValidatorInterface;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Result;

// Usually obtained from container
$validator = $container->get(ValidatorInterface::class);

$rules = [
    new Required(),
    new Number(min: 10),
    static function ($value): Result {
        $result = new Result();
        if ($value !== 42) {
            $result->addError('Value should be 42.');
            // or
            $result->addError('Value should be {value}.', ['value' => 42]);
        }

        return $result;
    }
];

$result = $validator->validate(41, $rules);
if (!$result->isValid()) {
    foreach ($result->getErrorMessages() as $error) {
        // ...
    }
}
```

### Validating a set of data

```php
use Yiisoft\Validator\DataSetInterface;
use Yiisoft\Validator\Validator;
use Yiisoft\Validator\ValidatorInterface;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Result;

final class MoneyTransfer implements DataSetInterface
{
    private $amount;
    
    public function __construct($amount) {
        $this->amount = $amount;
    }
    
    public function getAttributeValue(string $key){
        if (!isset($this->$key)) {
            throw new \InvalidArgumentException("There is no \"$key\" in MoneyTransfer.");
        }
        
        return $this->$key;
    }
}

// Usually obtained from container
$validator = $container->get(ValidatorInterface::class);

$moneyTransfer = new MoneyTransfer(142);
$rules = [    
    'amount' => [
        new Number(asInteger: true, max: 100),
        static function ($value): Result {
            $result = new Result();
            if ($value === 13) {
                $result->addError('Value should not be 13.');
            }
            return $result;
        }
    ],
];
$result = $validator->validate($moneyTransfer, $rules);
if ($result->isValid() === false) {
    foreach ($result->getErrors() as $error) {
        // ...
    }
}
```

#### Skipping validation on error

By default, if an error occurred during validation of an attribute, further rules for this attribute are processed. To 
change this behavior, use `skipOnError: true` when configuring rules:

```php
use Yiisoft\Validator\Rule\Number;

new Number(asInteger: true, max: 100, skipOnError: true)
```

#### Skipping empty values

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
use Yiisoft\Validator\SimpleRuleHandlerContainer;
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

#### Nested and related data

##### Basic usage

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

##### Other configuration options

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

##### Advanced usage

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

###### Using shortcut

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
`Nested` and `Each` pairs. If you need to that, please refer to example provided in "Basic usage" section.

###### Using keys containing separator / shortcut

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

#### Using attributes

##### Basic usage

Common flow is the same as you would use usual classes:

1. Declare property.
2. Add rules to it.

```php
use Yiisoft\Validator\Rule\Count;
use Yiisoft\Validator\Rule\Each;
use Yiisoft\Validator\Rule\Nested;
use Yiisoft\Validator\Rule\Number;

final class Chart
{
    #[Each([
        new Nested(Point::class),
    ])]
    private array $points;
}

final class Point
{
    #[Nested(Coordinates::class)]
    private $coordinates;
    #[Count(exactly: 3)]
    #[Each([
        new Number(min: 0, max: 255),
    ])]
    private array $rgb;
}

final class Coordinates
{
    #[Number(min: -10, max: 10)]
    private int $x;
    #[Number(min: -10, max: 10)]
    private int $y;
}
```

Here are some technical details:

- In case of a flat array `Point::$rgb`, a property type `array` needs to be declared.

Pass object directly to `validate()` method:

```php
use Yiisoft\Validator\ValidatorInterface;

// Usually obtained from container
$validator = $container->get(ValidatorInterface::class);

$coordinates = new Coordinates();
$errors = $validator->validate($coordinates)->getErrorMessagesIndexedByPath();
```

##### Traits

Traits are supported too:

```php
use Yiisoft\Validator\Rule\HasLength;

trait TitleTrait
{
    #[HasLength(max: 255)]
    private string $title;
}

final class Post
{
    use TitleTrait;
}
```

##### Callbacks

`Callback::$callback` property is not supported, also you can't use `callable` type with attributes. However,
`Callback::$method` can be set instead:

```php
<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Stub;

use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\Callback;
use Yiisoft\Validator\ValidationContext;

final class Author
{
    #[Callback(method: 'validateName')]
    private string $name;

    public static function validateName(mixed $value, object $rule, ValidationContext $context): Result
    {
        $result = new Result();
        if ($value !== 'foo') {
            $result->addError('Value must be "foo"!');
        }

        return $result;
    }
}
```

Note that the method must exist and have public and static modifiers.

##### Limitations

###### Nested attributes

PHP 8.0 supports attributes, but nested declaration is allowed only in PHP 8.1 and above.

So attributes such as `Each`, `Nested` and `Composite` are not allowed in PHP 8.0.

The following example is not allowed in PHP 8.0:

```php
use Yiisoft\Validator\Rule\Each;
use Yiisoft\Validator\Rule\Number;

final class Color
{
    #[Each([
        new Number(min: 0, max: 255),
    ])]
    private array $values;
}
```

But you can do this by creating a new composite rule from it.

```php
namespace App\Validator\Rule;

use Attribute;
use Yiisoft\Validator\Rule\Each;
use Yiisoft\Validator\Rule\Composite;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
final class RgbRule extends Composite
{
    public function getRules(): array
    {
        return [
            new Each([
                new Number(min: 0, max: 255),
            ]),
        ];
    }
}
```

And use it after as attribute.

```php
use App\Validator\Rule\RgbRule;

final class Color
{
    #[RgbRule]
    private array $values;
}

```

###### Function / method calls

You can't use a function / method call result with attributes. This problem can be overcome either with custom rule or
`Callback::$method` property. An example of custom rule:

```php
use Attribute;
use Yiisoft\Validator\FormatterInterface;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule;
use Yiisoft\Validator\RuleInterface
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\ValidationContext;

final class CustomFormatter implements FormatterInterface
{
    public function format(string $message, array $parameters = []): string
    {
        // More complex logic
        // ...
        return $message;
    }
}

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
final class ValidateXRule implements RuleInterface
{
    public function __construct(
        private $value,
    ) {
    }
    
    public function getValue()
    {
        return $this->value;
    }
}

final class Coordinates
{
    #[Number(min: -10, max: 10)]
    #[ValidateXRule()]
    private int $x;
    #[Number(min: -10, max: 10)]
    private int $y;
}
```

###### Passing instances

If you have PHP >= 8.1, you can utilize passing instances in attributes' scope. Otherwise, again fallback to custom
rules approach described above.

```php
use Yiisoft\Validator\Rule\HasLength;

final class Post
{
    #[HasLength(max: 255, formatter: new CustomFormatter())]
    private string $title;
}
```

### Requiring values

Use `Yiisoft\Validator\Rule\Required` rule to make sure value is provided. What values are considered empty can be 
customized via `$emptyCallback` option. Normalization is not performed here, so only a callable or special class is 
needed. For more details see "Skipping empty values" section.

```php
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\EmptyCriteria\WhenNull;

$rules = [new Required(emptyCallback: new WhenNull())];
```

### Conditional validation

In some cases there is a need to apply rule conditionally. It could be performed by using `when()`:

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

### Lazy or early exiting validation

When validation propagation is not needed `\Yiisoft\Validator\Rule\StopOnError` rule may be used.

```php
use Yiisoft\Validator\Rule\StopOnError;

new StopOnError([
    new Rule1();
    new Rule2();
    new Rule3();
])
```

When the validator get negative result of validation it stop all the rest rules inside `StopOnError`.
According to the example above, if `Rule2` fails `Rule3` won't even be tried to run.

### Validation rule handlers

#### Creating your own validation rule handlers

##### Basic usage

To create your own validation rule handler you should implement `RuleHandlerInterface`:

```php
namespace MyVendor\Rules;

use Yiisoft\Validator\DataSetInterface;
use Yiisoft\Validator\Exception\UnexpectedRuleException;use Yiisoft\Validator\FormatterInterface;use Yiisoft\Validator\Result;
use Yiisoft\Validator\RuleHandlerInterface;use Yiisoft\Validator\RuleInterface;

final class PiHandler implements RuleHandlerInterface
{
    use FormatMessageTrait;
    
    private FormatterInterface $formatter;
    
    public function __construct(
        ?FormatterInterface $formatter = null,
    ) {
        $this->formatter = $this->createFormatter();
    }
    
    public function validate(mixed $value, object $rule, ?ValidationContext $context = null): Result
    {
        if (!$rule instanceof Pi) {
            throw new UnexpectedRuleException(Pi::class, $rule);
        }
        
        $result = new Result();
        $equal = \abs($value - M_PI) < PHP_FLOAT_EPSILON;

        if (!$equal) {
            $result->addError($this->formatter->format('Value is not PI.'));
        }

        return $result;
    }
    
    private function createFormatter(): FormatterInterface
    {
        // More complex logic
        // ...
        return CustomFormatter();
    }
}
```

Note that third argument in `validate()` is an instance of `ValidationContext` so you can use it if you need
whole data set context. For example, implementation might be the following if you need to validate "company"
property only if "hasCompany" is true:

```php
namespace MyVendor\Rules;

use Yiisoft\Validator\DataSetInterface;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule;
use Yiisoft\Validator\ValidationContext;

final class CompanyNameHandler implements Rule\RuleHandlerInterface
{
    use FormatMessageTrait;
    
    private FormatterInterface $formatter;

    public function validate(mixed $value, object $rule, ?ValidationContext $context = null): Result
    {
        if (!$rule instanceof CompanyName) {
            throw new UnexpectedRuleException(CompanyName::class, $rule);
        }
        
        $result = new Result();
        $dataSet = $context->getDataSet();
        $hasCompany = $dataSet->getAttributeValue('hasCompany') === true;

        if ($hasCompany && $this->isCompanyNameValid($value) === false) {
            $result->addError('Company name is not valid.');
        }

        return $result;
    }

    private function isCompanyNameValid(string $value): bool
    {
        // check company name    
    }
}
```

> Note: Do not call handler's `validate()` method directly. It must be used via Validator only.

##### Resolving rule handler dependencies

Basically, you can use `SimpleRuleHandlerResolver` to resolve rule handler.
In case you need extra dependencies, this can be done by `ContainerRuleHandlerResolver`.

That would work with the following implementation:

```php
final class NoLessThanExistingBidRuleHandler implements RuleHandlerInterface
{
    use FormatMessageTrait;
    
    private FormatterInterface $formatter;

    public function __construct(    
        private ConnectionInterface $connection,        
        ?FormatterInterface $formatter = null
    ) {
        $this->formatter = $formatter ?? new Formatter();
    }
    }
    
    public function validate(mixed $value, object $rule, ?ValidationContext $context): Result
    {
        $result = new Result();
        
        $currentMax = $connection->query('SELECT MAX(price) FROM bid')->scalar();
        if ($value <= $currentMax) {
            $result->addError($this->formatter->format('There is a higher bid of {bid}.', ['bid' => $currentMax]));
        }

        return $result;
    }
}

$ruleHandlerContainer = new ContainerRuleHandlerResolver(new MyContainer());
$ruleHandler = $ruleHandlerContainer->resolve(NoLessThanExistingBidRuleHandler::class);
```

`MyContainer` is a container for resolving dependencies and  must be an instance of
`Psr\Container\ContainerInterface`. [Yii Dependency Injection](https://github.com/yiisoft/di) implementation also can
be used.

###### Using [Yii config](https://github.com/yiisoft/config)

```php
use Yiisoft\Di\Container;
use Yiisoft\Di\ContainerConfig;
use Yiisoft\Validator\RuleHandlerResolverInterface;
use Yiisoft\Validator\RuleHandlerContainer;

// Need to be defined in common.php
$config = [
    RuleHandlerResolverInterface::class => RuleHandlerContainer::class,
];

$containerConfig = ContainerConfig::create()->withDefinitions($config); 
$container = new Container($containerConfig);
$ruleHandlerResolver = $container->get(RuleHandlerResolverInterface::class);        
$ruleHandler = $ruleHandlerResolver->resolve(PiHandler::class);
```

#### Using common arguments for multiple rules of the same type

Because concrete rules' implementations (`Number`, etc.) are marked as final, you can not extend them to set up
common arguments. For this and more complex cases use composition instead of inheritance:

```php
use Yiisoft\Validator\RuleInterface;

final class Coordinate implements RuleInterface
{
    private Number $baseRule;
    
    public function __construct() 
    {
        $this->baseRule = new Number(min: -10, max: 10);
    }        

    public function validate(mixed $value, ?ValidationContext $context = null): Result
    {
        return $this->baseRule->validate($value, $context);
    }
}
```

### Rules

#### Passing single rule

```php
use Yiisoft\Validator\Validator;

/** @var Validator $validator */
$validator->validate(3, new Number(min: 5));
```

#### Grouping multiple validation rules

To reuse multiple validation rules it is advised to group rules like the following:

```php
use Yiisoft\Validator\Rule\HasLength;
use Yiisoft\Validator\Rule\Regex;
use \Yiisoft\Validator\Rule\Composite;

final class UsernameRule extends Composite
{
    public function getRules(): array
    {
        return [
            new HasLength(min: 2, max: 20),
            new Regex('~[a-z_\-]~i'),
        ];
    }
}
```

Then it could be used like the following:

```php
use Yiisoft\Validator\Validator;
use Yiisoft\Validator\ValidatorInterface;
use Yiisoft\Validator\Rule\Email;

// Usually obtained from container
$validator = $container->get(ValidatorInterface::class);

$rules = [
    'username' => new UsernameRule(),
    'email' => [new Email()],
];
$result = $validator->validate($user, $rules);

if ($result->isValid() === false) {
    foreach ($result->getErrors() as $error) {
        // ...
    }
}
```

## Testing

### Unit testing

The package is tested with [PHPUnit](https://phpunit.de/). To run tests:

```shell
./vendor/bin/phpunit
```

### Mutation testing

The package tests are checked with [Infection](https://infection.github.io/) mutation framework with
[Infection Static Analysis Plugin](https://github.com/Roave/infection-static-analysis-plugin). To run it:

```shell
./vendor/bin/roave-infection-static-analysis-plugin
```

### Static analysis

The code is statically analyzed with [Psalm](https://psalm.dev/). To run static analysis:

```shell
./vendor/bin/psalm
```

## License

The Yii Validator is free software. It is released under the terms of the BSD License.
Please see [`LICENSE`](./LICENSE.md) for more information.

Maintained by [Yii Software](https://www.yiiframework.com/).

## Support the project

[![Open Collective](https://img.shields.io/badge/Open%20Collective-sponsor-7eadf1?logo=open%20collective&logoColor=7eadf1&labelColor=555555)](https://opencollective.com/yiisoft)

## Follow updates

[![Official website](https://img.shields.io/badge/Powered_by-Yii_Framework-green.svg?style=flat)](https://www.yiiframework.com/)
[![Twitter](https://img.shields.io/badge/twitter-follow-1DA1F2?logo=twitter&logoColor=1DA1F2&labelColor=555555?style=flat)](https://twitter.com/yiiframework)
[![Telegram](https://img.shields.io/badge/telegram-join-1DA1F2?style=flat&logo=telegram)](https://t.me/yii3en)
[![Facebook](https://img.shields.io/badge/facebook-join-1DA1F2?style=flat&logo=facebook&logoColor=ffffff)](https://www.facebook.com/groups/yiitalk)
[![Slack](https://img.shields.io/badge/slack-join-1DA1F2?style=flat&logo=slack)](https://yiiframework.com/go/slack)
