# Using validator

Library could be used in two ways: validating a single value and validating a set of data.

## Validating a single value

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

## Validating a custom data set

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

## Validating an array

```php
use Yiisoft\Validator\Rule\AtLeast;
use Yiisoft\Validator\Rule\Count;
use Yiisoft\Validator\Rule\Email;
use Yiisoft\Validator\Rule\HasLength;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\Validator;

$data = [
    'name' => 'John',
    'age' => 17,
    'email' => 'jonh@example.com',
    'phone' => null,
];
$rules = [
    // The rules that are not related to a specific attribute

    // An array must contain exactly 4 items.
    new Count(4),
    // At least one of the attributes ("email" and "phone") must be passed and have non-empty value.  
    new AtLeast(['email', 'phone']),

    // The rules related to a specific attribute.

    'name' => [
        new Required(), // The name is required (must be passed and have non-empty value).
        new HasLength(min: 2), // The name's length must be no less than 2 characters.
    ],  
    'age' => new Number(min: 21), // The age must be at least 21 years.  
    'email' => new Email(), // Email must be a valid email address.  
];
$result = (new Validator())->validate($data, $rules);
```

## Validating an object

Similar to arrays, it's possible to validate the object both as a whole and by individual properties.

For objects there is an additional option to configure validation with PHP attributes which allows to not pass the rules
separately in explicit way (passing just the object itself is enough). For example:

```php
use Yiisoft\Validator\Rule\AtLeast;
use Yiisoft\Validator\Rule\Email;
use Yiisoft\Validator\Rule\HasLength;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\Validator;

#[AtLeast(['email', 'phone'])]
final class Person
{
    public function __construct(
        #[Required]
        #[HasLength(min: 2)]
        public readonly ?string $name = null,

        #[Number(min: 21)]
        public readonly ?int $age = null,

        #[Email]
        public readonly ?string $email = null,

        public readonly ?string $phone = null,
    ) {
    }
}

$person = new Person('Jonh', 17, 'jonh@example.com', null);
$result = (new Validator())->validate($person);
```

> **Note:** `readonly` properties are supported only starting from PHP 8.1. 

## Rules

### Passing single rule

```php
use Yiisoft\Validator\Validator;

/** @var Validator $validator */
$validator->validate(3, new Number(min: 5));
```

### Grouping multiple validation rules

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
