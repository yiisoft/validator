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
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/yiisoft/validator/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/yiisoft/validator/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/yiisoft/validator/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/yiisoft/validator/?branch=master)
[![Mutation testing badge](https://img.shields.io/endpoint?style=flat&url=https%3A%2F%2Fbadge-api.stryker-mutator.io%2Fgithub.com%2Fyiisoft%2Fwidget%2Fmaster)](https://dashboard.stryker-mutator.io/reports/github.com/yiisoft/validator/master)
[![static analysis](https://github.com/yiisoft/validator/workflows/static%20analysis/badge.svg)](https://github.com/yiisoft/validator/actions?query=workflow%3A%22static+analysis%22)
[![type-coverage](https://shepherd.dev/github/yiisoft/validator/coverage.svg)](https://shepherd.dev/github/yiisoft/validator)

## Features

- Could be used with any object.
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
composer require yiisoft/validator --prefer-dist
```

## General usage

Library could be used in two ways: validating a single value and validating a set of data.

### Validating a single value

```php
use Yiisoft\Validator\RuleSet;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Result;

$ruleSet = new RuleSet([
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
]);

$result = $ruleSet->validate(41);
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

$validator = new Validator(); // Usually obtained from container
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
$resultSet = $validator->validate($moneyTransfer, $rules);
foreach ($resultSet as $attribute => $result) {
    if ($result->isValid() === false) {
        foreach ($result->getErrors() as $error) {
            // ...
        }
    }
}
```

#### Skipping validation on error

By default, if an error occurred during validation of an attribute, further rules for this attribute are processed.
To change this behavior use `skipOnError: true` when configuring rules:  

```php
use Yiisoft\Validator\Rule\Number;

new Number(asInteger: true, max: 100, skipOnError: true)
```

#### Skipping empty values

By default, empty values are validated. That is undesirable if you need to allow not specifying a field.
To change this behavior use `skipOnEmpty: true`:

```php
use Yiisoft\Validator\Rule\Number;

new Number(asInteger: true, max: 100, skipOnEmpty: true)
```

#### Nested and related data

In many cases there is a need to validate related data in addition to current entity / model. There is a `Nested` rule 
for this purpose:

```php
use Yiisoft\Validator\Rule\HasLength;
use Yiisoft\Validator\Rule\Nested;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Rule\Required;

$validator = getValidator();
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

A more complex real-life example is a chart that is made of points. This data is represented as arrays. `Nested` can be 
combined with `Each` rule to validate such similar structures:

```php
use Yiisoft\Validator\Rule\Count;
use Yiisoft\Validator\Rule\Each;
use Yiisoft\Validator\Rule\Nested;
use Yiisoft\Validator\RuleSet;

$validator = getValidator();
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

#### Using attributes

##### Basic usage

You can also use attributes as an alternative. Declare the DTOs, relations and rules:

```php
use Yiisoft\Validator\Attribute\HasMany;
use Yiisoft\Validator\Attribute\HasOne;
use Yiisoft\Validator\Rule\Number;

final class ChartsData
{
    #[HasMany(Chart::class)]
    private array $charts;
}

final class Chart
{
    #[HasMany(Point::class)]
    private array $points;
}

final class Point
{
    #[HasOne(Coordinates::class)]
    private $coordinates;
    #[Number(min: 0, max: 255)]
    private array $rgb; // A flat array, the "Number" rule will be applied to each array element.
}

final class Coordinates
{
    #[Number(min: -10, max: 10)]
    private int $x;
    #[Number(min: -10, max: 10)]
    private int $y;
}
```

To combine both flat rules and "each" rules, specify `Each` explicitly and place flat rules above "each" ones:

```php
use Yiisoft\Validator\Rule\Count;
use Yiisoft\Validator\Rule\Each;
use Yiisoft\Validator\Attribute\HasMany;
use Yiisoft\Validator\Attribute\HasOne;
use Yiisoft\Validator\Rule\Number;

final class Point
{
    #[HasOne(Coordinates::class)]
    private $coordinates;
    #[Count(exactly: 3)]
    #[Each()]
    #[Number(min: 0, max: 255)]
    private array $rgb;
}
```

In this example `Count` will be applied to the whole value and `Number` - for each item.

Here are some technical details:

- `HasOne` uses `Nested` rule.
- `HasMany` uses combination of `Each` and `Nested` rules.
- In case of a flat array `Point::$rgb`, a property type `array` needs to be declared. It uses `Each` rule internally.

Pass the base DTO to `AttributeDataSet` and use it for validation.

```php
use Yiisoft\Validator\DataSet\AttributeDataSet;
use Yiisoft\Validator\Validator;

$data = [
    // ...
];
$dataSet = new AttributeDataSet(new ChartsData(), $data);
$validator = new Validator();
$errors = $validator->validate($dataSet)->getErrorMessagesIndexedByPath();
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

##### Limitations

This approach has some limitations.

###### `Each` and `Nested` rules

`Each` and `Nested` rules are not supported directly. Use `HasOne` and `HasMany` attributes for declaring relations (or 
property type `array` for flat rules) instead. Use `Each` and `Nested` rules in addition for custom configuration if 
needed.

```php
use Yiisoft\Validator\Attribute\HasMany;
use Yiisoft\Validator\Attribute\HasOne;
use Yiisoft\Validator\Rule\Each;
use Yiisoft\Validator\Rule\Nested;
use Yiisoft\Validator\Rule\Number;

final class ChartsData
{
    #[Each(incorrectInputMessage: 'Custom message 1.', message: 'Custom message 2.')]
    #[Nested(errorWhenPropertyPathIsNotFound: true, propertyPathIsNotFoundMessage: 'Custom message 3.')]
    #[HasMany(Chart::class)]
    private array $charts;
}

final class Point
{
    #[Nested(errorWhenPropertyPathIsNotFound: true, propertyPathIsNotFoundMessage: 'Custom message 4.')]
    #[HasOne(Coordinates::class)]
    private $coordinates;
    #[Each(incorrectInputMessage: 'Custom message 5.', message: 'Custom message 6.')]
    #[Number(min: 0, max: 255)]
    private array $rgb;
}
```

###### `Callback` rule and `callable` type

`Callback` rule is not supported, also you can't use `callable` type with attributes. Use custom rule instead.

```php
use Attribute;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Rule\RuleHandlerInterface;
use Yiisoft\Validator\ValidationContext;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class ValidateXRuleHandler implements RuleHandlerInterface
{    
    public function validate(mixed $value, object $rule, ?ValidationContext $context = null): Result
    {
        $result = new Result();
        $result->addError('Custom error.');

        return $result;
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

###### `GroupRule`

`GroupRule` is not supported, but it's unnecessary since multiple attributes can be used for one property (except they 
must be of different type).

```php
use Yiisoft\Validator\Rule\HasLength;
use Yiisoft\Validator\Rule\Regex;

final class UserData
{
    #[HasLength(min: 2, max: 20)]
    #[Regex('~[a-z_\-]~i')]
    private string $name;    
}
```

###### Function / method calls

You can't use a function / method call result with attributes. Like with `Callback` rule and callable, this problem can 
be overcome with custom rule.

```php
use Attribute;
use Yiisoft\Validator\FormatterInterface;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule;
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

#[Attribute(Attribute::TARGET_PROPERTY)]
final class ValidateXRule implements \Yiisoft\Validator\RuleInterface
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

### Conditional validation

In some cases there is a need to apply rule conditionally. It could be performed by using `when()`:

```php
use Yiisoft\Validator\Rule\Number;

new Number(
    when: static function ($value, DataSetInterface $dataSet) {
        return $dataSet->getAttributeValue('country') === Country::USA;
    },
    asInteger: true, 
    min: 100
);
```

If callable returns `true` rule is applied, when the value returned is `false`, rule is skipped.

### Creating your own validation rule handlers

#### Basic usage

To create your own validation rule handler you should implement `RuleHandlerInterface`:

```php
namespace MyVendor\Rules;

use Yiisoft\Validator\DataSetInterface;
use Yiisoft\Validator\Exception\UnexpectedRuleException;use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\RuleHandlerInterface;use Yiisoft\Validator\RuleInterface;

final class Pi implements RuleHandlerInterface
{
    public function __construct(
        ?FormatterInterface $formatter = null,
    ) {
        $this->formatter = $this->createFormatter();
    }
    
    public function validate(mixed $value, object $rule, ?ValidationContext $context = null): Result
    {
        if (!$rule instanceof Pi) {
            throw new UnexpectedRuleException($rule);
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

final class CompanyName implements Rule\RuleHandlerInterface
{
    public function validate(mixed $value, object $rule, ?ValidationContext $context = null): Result
    {
        if (!$rule instanceof CompanyName) {
            throw new UnexpectedRuleException($rule);
        }
        
        $result = new Result();
        $dataSet = $context->getDataSet();
        $hasCompany = $dataSet !== null && $dataSet->getAttributeValue('hasCompany') === true;

        if ($hasCompany && $this->isCompanyNameValid($value) === false) {
            $result->addError($this->formatMessage('Company name is not valid.'));
        }

        return $result;
    }

    private function isCompanyNameValid(string $value): bool
    {
        // check company name    
    }
}
```

In case you need extra dependencies, these could be passed to the rule when it is created:

```php
$rule = new NoLessThanExistingBidRule($connection);
```

That would work with the following implementation:

```php
final class NoLessThanExistingBidRule extends Rule
{   
    public function __construct(    
        private ConnectionInterface $connection,        
        ?FormatterInterface $formatter = null,
        bool $skipOnEmpty = false,
        bool $skipOnError = false,
        $when = null
    ) {
        parent::__construct(formatter: $formatter, skipOnEmpty: $skipOnEmpty, skipOnError: $skipOnError, when: $when);
    }
    
    protected function validateValue($value, DataSetInterface $dataSet = null): Result
    {
        $result = new Result();
        
        $currentMax = $connection->query('SELECT MAX(price) FROM bid')->scalar();
        if ($value <= $currentMax) {
            $result->addError($this->formatMessage('There is a higher bid of {bid}.', ['bid' => $currentMax]));
        }

        return $result;
    }
}
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

### Grouping multiple validation rules

To reuse multiple validation rules it is advised to group rules like the following:

```php
use Yiisoft\Validator\RuleSet;
use Yiisoft\Validator\Rule\HasLength;
use Yiisoft\Validator\Rule\Regex;
use \Yiisoft\Validator\Rule\GroupRule;

final class UsernameRule extends GroupRule
{
    public function getRuleSet(): RuleSet
    {
        return new RuleSet([
            new HasLength(min: 2, max: 20),
            new Regex('~[a-z_\-]~i'),
        ]);
    }
}
```

Then it could be used like the following:

```php
use Yiisoft\Validator\Validator;
use Yiisoft\Validator\Rule\Email;

$validator = new Validator();
$rules = [
    'username' => new UsernameRule(),
    'email' => [new Email()],
];
$results = $validator->validate($user, $rules);

foreach ($results as $attribute => $result) {
    if ($result->isValid() === false) {
        foreach ($result->getErrors() as $error) {
            // ...
        }
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
