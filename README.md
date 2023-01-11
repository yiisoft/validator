<p align="center">
    <a href="https://github.com/yiisoft" target="_blank">
        <img src="https://yiisoft.github.io/docs/images/yii_logo.svg" height="100px">
    </a>
    <h1 align="center">Yii Validator</h1>
    <br>
</p>

[![Latest Stable Version](https://poser.pugx.org/yiisoft/validator/v/stable.png)](https://packagist.org/packages/yiisoft/validator)
[![Total Downloads](https://poser.pugx.org/yiisoft/validator/downloads.png)](https://packagist.org/packages/yiisoft/validator)
[![Build status](https://github.com/yiisoft/validator/workflows/build/badge.svg)](https://github.com/yiisoft/validator/actions?query=workflow%3Abuild)
[![Code Coverage](https://codecov.io/gh/yiisoft/validator/branch/master/graph/badge.svg)](https://codecov.io/gh/yiisoft/validator)
[![Mutation testing badge](https://img.shields.io/endpoint?style=flat&url=https%3A%2F%2Fbadge-api.stryker-mutator.io%2Fgithub.com%2Fyiisoft%2Fwidget%2Fmaster)](https://dashboard.stryker-mutator.io/reports/github.com/yiisoft/validator/master)
[![type-coverage](https://shepherd.dev/github/yiisoft/validator/coverage.svg)](https://shepherd.dev/github/yiisoft/validator)
[![static analysis](https://github.com/yiisoft/validator/workflows/static%20analysis/badge.svg)](https://github.com/yiisoft/validator/actions?query=workflow%3A%22static+analysis%22)
[![psalm-level](https://shepherd.dev/github/yiisoft/validator/level.svg)](https://shepherd.dev/github/yiisoft/validator)

The package provides data validation capabilities.

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
- `JSON` PHP extension.
- `mbstring` PHP extension.

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

### Requiring values

Use `Yiisoft\Validator\Rule\Required` rule to make sure value is provided. What values are considered empty can be 
customized via `$emptyCallback` option. Normalization is not performed here, so only a callable or special class is 
needed. For more details see "Skipping empty values" section.

```php
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\EmptyCriteria\WhenNull;

$rules = [new Required(emptyCallback: new WhenNull())];
```

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
use Yiisoft\Validator\RuleHandlerResolver\RuleHandlerContainer;

// Needs to be defined in common.php
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

### Code style

Use [Rector](https://github.com/rectorphp/rector) to make codebase follow some specific rules or
use either newest or any specific version of PHP:

```shell
./vendor/bin/rector
```

### Dependencies

Use [ComposerRequireChecker](https://github.com/maglnet/ComposerRequireChecker) to detect transitive
[Composer](https://getcomposer.org/) dependencies.

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
