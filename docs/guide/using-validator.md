# Using validator

Validator allows to check data in any format. Here are some of the most used cases.

## Validating a single value

In the simplest case the validator can be used to check a single value:

```php
use Yiisoft\Validator\Rule\HasLength;
use Yiisoft\Validator\Rule\Regex;
use Yiisoft\Validator\Validator;

$value = 'mrX';
$rules = [
    new HasLength(min: 4, max: 20),
    new Regex('~^[a-z_\-]*$~i'),
];
$result = (new Validator())->validate($value, $rules);
```

> **Note:** Use `Each` rule to validate multiple values of the same type.

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

It's possible to validate an array both as a whole and by individual items. For example:

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
    'email' => 'john@example.com',
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
        // The name is required (must be passed and have non-empty value).
        new Required(),
        // The name's length must be no less than 2 characters.
        new HasLength(min: 2),
    ],  
    'age' => new Number(min: 21), // The age must be at least 21 years.  
    'email' => new Email(), // Email must be a valid email address.  
];
$result = (new Validator())->validate($data, $rules);
```

> **Note:** Use `Nested` rule to validate nested arrays and `Each` rule to validate multiple arrays.

## Validating an object

Similar to arrays, it's possible to validate an object both as a whole and by individual properties.

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

$person = new Person(name: 'John', age: 17, email: 'john@example.com', phone: null);
$result = (new Validator())->validate($person);
```

> **Note:** `readonly` properties are supported only starting from PHP 8.1.

> **Note:** Use `Nested` rule to validate related objects and `Each` rule to validate multiple objects. 

## Rules

### Passing single rule

For a single rule there is an option to omit array:

```php
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Validator;

$value = 7;
$rule = new Number(min: 42);
$result = (new Validator())->validate($value, $rule);
```
