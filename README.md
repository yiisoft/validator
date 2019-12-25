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

class MoneyTransfer implements DataSetInterface
{
    private $amount;
    
    public function __construct($amount) {
        $this->amount = $amount;
    }
    
    public function getValue(string $key){
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

### Creating your own validation rules

In order to create your own validation rule you should extend `Rule` class:

```php
namespace MyVendor\Rules;

use Yiisoft\Validator\DataSetInterface;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule;

class Pi extends Rule
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
use Yiisoft\Validator\Rule;

class CompanyName extends Rule
{
    protected function validateValue($value, DataSetInterface $dataSet = null): Result
    {
        $result = new Result();
        $hasCompany = $dataSet !== null && $dataSet->getValue('hasCompany') === true;

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

### Grouping common validation rules into rule sets

In order to reuse multiple validation rules it is advised to group rules into validation sets:

```php
use Yiisoft\Validator\Rule\HasLength;
use Yiisoft\Validator\Rule\MatchRegularExpression;

class UsernameRules
{
    public static function get(): array
    {
        return [
            (new HasLength)->min(2)->max(20),
            new MatchRegularExpression('~[a-z_\-]~i')
        ];
    }
}
```

Then it could be used like the following:

```php
use Yiisoft\Validator\Validator;
use Yiisoft\Validator\Rule\Email;

$validator = new Validator([    
    'username' => UsernameRules::get(),
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

 
