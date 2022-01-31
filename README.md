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

* [Yii Validator](#yii-validator)
    * [Features](#features)
    * [Requirements](#requirements)
    * [Installation](#installation)
    * [General usage](#general-usage)
        * [Validating a single value](#validating-a-single-value)
        * [Validating a set of data](#validating-a-set-of-data)
            * [Skipping validation on error](#skipping-validation-on-error)
            * [Skipping empty values](#skipping-empty-values)
            * [Nested and related data](#nested-and-related-data)
        * [Conditional validation](#conditional-validation)
        * [Creating your own validation rules](#creating-your-own-validation-rules)
        * [Grouping multiple validation rules](#grouping-multiple-validation-rules)
        * [Setting up your own formatter](#setting-up-your-own-formatter)
    * [Testing](#testing)
        * [Unit testing](#unit-testing)
        * [Mutation testing](#mutation-testing)
        * [Static analysis](#static-analysis)
    * [License](#license)
    * [Support the project](#support-the-project)
    * [Follow updates](#follow-updates)

## Features

- Could be used with any object.
- Skip further validation if an error occurred for the same field.
- Skip validation of empty value.
- Error message formatting.
- Conditional validation.
- Could pass context to validation rule.
- Common rules bundled.

## Requirements

- PHP 7.4 or higher.

## Installation

The package could be installed with composer:

```shell
composer require yiisoft/validator --prefer-dist
```

## General usage

Library could be used in two ways: validating a single value and validating a set of data.

### Validating a single value

```php
use Yiisoft\Validator\Rules;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Result;

$rules = new Rules([
    Required::rule(),
    Number::rule()->min(10),
    static function ($value): Result {
        $result = new Result();
        if ($value !== 42) {
            $result->addError('Value should be 42.');
        }
        return $result;
    }
]);

$result = $rules->validate(41);
if ($result->isValid() === false) {
    foreach ($result->getErrors() as $error) {
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
    
    public function hasAttribute(string $attribute) : bool
    {
        return isset($this->$key);
    }
}

$validator = new Validator(); // Usually obtained from container
$moneyTransfer = new MoneyTransfer(142);
$rules = [    
    'amount' => [
        Number::rule()->integer()->max(100),
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

By default, if an error occurred during validation of an attribute, further rules for this attribute are skipped.
To change this behavior use `skipOnError(false)` when configuring rules:  

```php
Number::rule()->integer()->max(100)->skipOnError(false)
```

#### Skipping empty values

By default, empty values are validated. That is undesirable if you need to allow not specifying a field.
To change this behavior use `skipOnEmpty(true)`:

```php
Number::rule()->integer()->max(100)->skipOnEmpty(true)
```

#### Nested and related data

In many cases there is a need to validate related data in addition to current entity / model. There is a `Nested` rule 
for this purpose:

```php
use Yiisoft\Validator\Rule\HasLength;
use Yiisoft\Validator\Rule\Nested;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Rule\Required;

$data = ['author' => ['name' => 'Alexey', 'age' => '31']];
$rule = Nested::rule([
    'title' => [Required::rule()],
    'author' => Nested::rule([
        'name' => [HasLength::rule()->min(3)],
        'age' => [Number::rule()->min(18)],
    )];
]);
$errors = $rule->validate($data)->getErrors();
```

A more complex real-life example is a chart that is made of points. This data is represented as arrays. `Nested` can be 
combined with `Each` rule to validate such similar structures:

```php
use Yiisoft\Validator\Rule\Each;
use Yiisoft\Validator\Rule\Nested;

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
$rule = Nested::rule([
    'charts' => Each::rule(new Rules([
        Nested::rule([
            'points' => Each::rule(new Rules([
                Nested::rule([
                    'coordinates' => Nested::rule([
                        'x' => [Number::rule()->min(-10)->max(10)],
                        'y' => [Number::rule()->min(-10)->max(10)],
                    ]),
                    'rgb' => Each::rule(new Rules([
                        Number::rule()->min(0)->max(255)->skipOnError(false),
                    ])),
                ])->skipOnError(false),
            ])),
        ])->skipOnError(false),
    ])),
]);
$errors = $rule->validate($data)->getErrors();
```

The contents of the errors will be:

```php
$errors = [
    'charts.0.points.0.coordinates.x' => 'Value must be no less than -10.',
    'charts.0.points.0.rgb.0' => 'Value must be no less than 0. -1 given.',
    // ...
];
```

##### Attributes

If you have PHP 8, you can use attributes. Declare the DTOs, relations and rules:

```php
use Yiisoft\Validator\Attribute\HasMany;
use Yiisoft\Validator\Attribute\HasOne;
use Yiisoft\Validator\Attribute\Validate;
use Yiisoft\Validator\DataSet\AnnotatedDataSet;
use Yiisoft\Validator\Rule\Each;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Validator;

class ChartsData
{
    #[HasMany(Chart::class)]
    private array $charts;
}

class Chart
{
    #[HasMany(Dot::class)]
    private array $dots;
}

class Dot
{
    #[HasOne(Coordinates::class)]
    private $coordinates;
    #[Validate(Each::class)]
    #[Validate(Number::class, ['min' => 0, 'max' => 255, 'skipOnError' => false])]
    private array $rgb;
}

class Coordinates
{
    #[Validate(Number::class, ['min' => -10, 'max' => 10])]
    private int $x;
    #[Validate(Number::class, ['min' => -10, 'max' => 10])]
    private int $y;
}
```

Retrieve rule from the base DTO and use it for validation.

```php
// The structure of data and errors is the same as in previous example
$data = [];
$dataSet = new AnnotatedDataSet(new ChartsData(), $data);
$errors = $rule->validate($dataSet)->getErrors();
```

### Conditional validation

In some cases there is a need to apply rule conditionally. It could be performed by using `when()`:

```php
Number::rule()->integer()->min(100)->when(static function ($value, DataSetInterface $dataSet) {
    return $dataSet->getAttributeValue('country') === Country::USA;
});
```

If callable returns `true` rule is applied, when the value returned is `false`, rule is skipped.

### Creating your own validation rules

To create your own validation rule you should extend `Rule` class:

```php
namespace MyVendor\Rules;

use Yiisoft\Validator\DataSetInterface;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule;

final class Pi extends Rule
{
    protected function validateValue($value, DataSetInterface $dataSet = null): Result
    {
        $result = new Result();
        $equal = \abs($value - M_PI) < PHP_FLOAT_EPSILON;
        if (!$equal) {
            $result->addError($this->formatMessage('Value is not PI.'));
        }
        return $result;
    }
}
```

Note that `validateValue()` second argument is an instance of `DataSetInterface` so you can use it if you need
whole data set context. For example, implementation might be the following if you need to validate "company"
property only if "hasCompany" is true:

```php
namespace MyVendor\Rules;

use Yiisoft\Validator\DataSetInterface;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule;

final class CompanyName extends Rule
{
    protected function validateValue($value, DataSetInterface $dataSet = null): Result
    {
        $result = new Result();
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
$rule = NoLessThanExistingBidRule::rule($connection);
```

That would work with the following implementation:

```php
final class NoLessThanExistingBidRule extends Rule
{
    private ConnectionInterface $connection;

    public static function rule(ConnectionInterface $connection): self
    {
        $rule = new self();
        $rule->connection = $connection;
        return $rule;
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

### Grouping multiple validation rules

To reuse multiple validation rules it is advised to group rules like the following:

```php
use Yiisoft\Validator\Rules;
use Yiisoft\Validator\Rule\HasLength;
use Yiisoft\Validator\Rule\MatchRegularExpression;
use \Yiisoft\Validator\Rule\GroupRule;

final class UsernameRule extends GroupRule
{
    public function getRules(): Rules
    {
        return new Rules([
            HasLength::rule()->min(2)->max(20),
            MatchRegularExpression::rule('~[a-z_\-]~i')
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
    'username' => UsernameRule::rule(),
    'email' => [Email::rule()],
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

### Setting up your own formatter

If you want to customize error message formatter in a certain case you need to use immutable `withFormatter()` method:

```php
use Yiisoft\Validator\Validator;

final class PostController
{
    public function actionIndex(Validator $validator): ResponseInterface
    {
        // ...
        $result = $validator->withFormatter(new CustomFormatter())->validate($dataSet, $rules);
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
