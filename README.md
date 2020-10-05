<p align="center">
    <a href="https://github.com/yiisoft" target="_blank">
        <img src="https://avatars0.githubusercontent.com/u/993323" height="100px">
    </a>
    <h1 align="center">Yii Validator</h1>
    <br>
</p>

The package provides data validation capabilities.

[![Latest Stable Version](https://poser.pugx.org/yiisoft/validator/v/stable.png)](https://packagist.org/packages/yiisoft/validator)
[![Total Downloads](https://poser.pugx.org/yiisoft/validator/downloads.png)](https://packagist.org/packages/yiisoft/validator)
[![Build Status](https://travis-ci.com/yiisoft/validator.svg?branch=master)](https://travis-ci.com/yiisoft/validator)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/yiisoft/validator/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/yiisoft/validator/?branch=master)

## Features

- Could be used with any object.
- Skip further validation if an error occurred for the same field.
- Skip validation of empty value.
- Error message translations.
- Conditional validation.
- Could pass context to validation rule.
- Common rules bundled.

## General usage

Library could be used in two ways: validating a single value and validating a set of data.

### Validating a single value

```php
use Yiisoft\Validator\Rules;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Result;

$rules = new Rules([
    new Required(),
    (new Number())->min(10),
    static function ($value): Result {
        $result = new Result();
        if ($value !== 42) {
            $result->addError('Value should be 42!');
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
}

$moneyTransfer = new MoneyTransfer(142);

$validator = new Validator([    
    'amount' => [
        (new Number())->integer()->max(100),
        static function ($value): Result {
            $result = new Result();
            if ($value === 13) {
                $result->addError('Value should not be 13!');
            }
            return $result;
        }
    ],
]);

$results = $validator->validate($moneyTransfer);
foreach ($results as $attribute => $result) {
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
(new Number())->integer()->max(100)->skipOnError(false)
```

#### Skipping empty values

By default, empty values are validated. That is undesirable if you need to allow not specifying a field.
To change this behavior use `skipOnEmpty(true)`:

```php
(new Number())->integer()->max(100)->skipOnEmpty(true)
```

### Conditional validation

In some cases there is a need to apply rule conditionally. It could be performed by using `when()`:

```php
(new Number())->integer()->min(100)->when(static function ($value, DataSetInterface $dataSet) {
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
use Yiisoft\Validator\AbstractRule;

final class Pi extends AbstractRule
{
    protected function validateValue($value, DataSetInterface $dataSet = null): Result
    {
        $result = new Result();
        if ($value != M_PI) {
            $result->addError('Value is not PI');
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
use Yiisoft\Validator\AbstractRule;

final class CompanyName extends AbstractRule
{
    protected function validateValue($value, DataSetInterface $dataSet = null): Result
    {
        $result = new Result();
        $hasCompany = $dataSet !== null && $dataSet->getAttributeValue('hasCompany') === true;

        if ($hasCompany && $this->isCompanyNameValid($value) === false) {
            
            $result->addError('Company name is not valid');
        }
        return $result;
    }
    
    private function isCompanyNameValid(string $value): bool
    {
        // check company name    
    }
}
```

### Grouping multiple validation rules

To reuse multiple validation rules it is advised to group rules like the following:

```php
use Yiisoft\Validator\Rules;
use Yiisoft\Validator\Rule\HasLength;
use Yiisoft\Validator\Rule\MatchRegularExpression;
use \Yiisoft\Validator\Rule\GroupAbstractRule;

final class UsernameRule extends GroupAbstractRule
{
    public function getRules(): Rules
    {
        return new Rules([
            (new HasLength())->min(2)->max(20),
            new MatchRegularExpression('~[a-z_\-]~i')
        ]);
    }
}
```

Then it could be used like the following:

```php
use Yiisoft\Validator\Validator;
use Yiisoft\Validator\Rule\Email;

$validator = new Validator([    
    'username' => new UsernameRule(),
    'email' => [new Email()]
]);

$results = $validator->validate($user);
foreach ($results as $attribute => $result) {
    if ($result->isValid() === false) {
        foreach ($result->getErrors() as $error) {
            // ...
        }
    }
}
```

 
