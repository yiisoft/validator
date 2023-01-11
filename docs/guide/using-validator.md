## Using validator

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
