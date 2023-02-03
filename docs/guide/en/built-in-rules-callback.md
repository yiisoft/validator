# `Callback` - a wrapper around `callable`

This rule allows validation of the current attribute value (but not limited to it) with an arbitrary condition within a  
callable. The benefit is that there is no need to create a separate custom rule and handler.

A condition can be within:

- Standalone callable function.
- Callable class.
- DTO (data transfer object) method.

The downside of using standalone functions and DTO methods is a lack of reusability. So they are mainly useful 
for some specific non-repetitive conditions. Reusability can be achieved with callable classes, but depending on other
factors (the need for additional parameters for example), it might be a good idea to create a full-fledged
[custom rule](creating-custom-rules.md) with a separate handler instead.

The callback function signature is the following:

```php
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\Callback;
use Yiisoft\Validator\ValidationContext;

function (mixed $value, Callback $rule, ValidationContext $context): Result;
```

where:

- `$value` is the validated value;
- `$rule` is a reference to the original `Callback` rule;
- `$context` is a validation context;
- returned value is a validation result instance with or without errors.

## Using as a function

An example of passing a standalone callable function to a `Callback` rule:

```php
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\Callback;
use Yiisoft\Validator\ValidationContext;

new Callback(
    static function (mixed $value, Callback $rule, ValidationContext $context): Result {
        // The actual validation code.
        
        return new Result();
    },
);
```

## Examples

### Value validation

`Callback` rule can be used to add validation missing in built-in rules for a single attribute's value. Below is the 
example verifying that a value is a valid [YAML] string (additionally requires `yaml` PHP extension):

```php
use Exception;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\Callback;

new Callback(
    static function (mixed $value): Result {
        if (!is_string($value)) {
            return (new Result())->addError('This value must be a string.');
        }

        $notYamlMessage = 'This value is not a valid YAML.';

        try {
            $data = yaml_parse($value);
        } catch (Exception $e) {
            return (new Result())->addError($notYamlMessage);
        }

        if ($data === false) {
            return (new Result())->addError($notYamlMessage);
        }

        return new Result();
    },
);
```

> **Note:** Processing untrusted user input with `yaml_parse()` can be dangerous with certain settings. Please refer to
> [yaml_parse docs] for more details. 

### Usage of validation context for validating multiple attributes depending on each other

In the example below, the 3 angles are validated as degrees to form
a valid triangle:

```php
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\Callback;
use Yiisoft\Validator\Rule\Integer;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\ValidationContext;

$rules = [
    'angleA' => [
        new Required(),
        new Integer(),
    ],
    'angleB' => [
        new Required(),
        new Integer(),
    ],
    'angleC' => [
        new Required(),
        new Integer(),
    ],

    new Callback(
        static function (mixed $value, Callback $rule, ValidationContext $context): Result {
            $angleA = $context->getDataSet()->getAttributeValue('angleA');
            $angleB = $context->getDataSet()->getAttributeValue('angleB');
            $angleC = $context->getDataSet()->getAttributeValue('angleC');
            $sum = $angleA + $angleB + $angleC;
            
            if ($sum <= 0) {
                return (new Result())->addError('The angles\' sum can\'t be negative.');
            } 
            
            if ($sum > 180) {
                return (new Result())->addError('The angles\' sum can\'t be greater than 180 degrees.');
            }
            
            return new Result();
        }
    ),
];
```

### Replacing boilerplate code with separate rules and `when`

However, some cases of using validation context can lead to boilerplate code:

```php
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\Callback;
use Yiisoft\Validator\ValidationContext;

static function (mixed $value, Callback $rule, ValidationContext $context): Result {
    if ($context->getDataSet()->getAttributeValue('married') === false) {
        return new Result();
    }
    
    $spouseAge = $context->getDataSet()->getAttributeValue('spouseAge');
    if ($spouseAge === null) {
        return (new Result())->addError('Spouse age is required.');
    }
    
    if (!is_int($spouseAge)) {
        return (new Result())->addError('Spouse age must be an integer.');
    }
    
    if ($spouseAge < 18 || $spouseAge > 100) {
        return (new Result())->addError('Spouse age must be between 18 and 100.');
    }        
    
    return new Result();
};
```

They can be rewritten using multiple rules and conditional validation making code more intuitive. We can use built-in
rules where possible:

```php
use Yiisoft\Validator\Rule\BooleanValue;
use Yiisoft\Validator\Rule\Integer;
use Yiisoft\Validator\ValidationContext;

$rules = [
    'married' => new BooleanValue(),
    'spouseAge' => new Integer(
        min: 18,
        max: 100,
        when: static function (mixed $value, ValidationContext $context): bool {
            return $context->getDataSet()->getAttributeValue('married') === true;
        },
    ),
];
```

## Using as an object's method

### For property

When using as a PHP attribute, set an object's method as a callback instead:

```php
use Exception;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\Callback;

final class Config {
    public function __construct(
        #[Callback(method: 'validateYaml')]
        private string $yaml,
    ) {
    }

    private function validateYaml(mixed $value): Result 
    {
        if (!is_string($value)) {
            return (new Result())->addError('This value must be a string.');
        }
        
        $notYamlMessage = 'This value is not a valid YAML.';

        try {
            $data = yaml_parse($value);
        } catch (Exception $e) {
            return (new Result())->addError($notYamlMessage);
        }
        
        if ($data === false) {
            return (new Result())->addError($notYamlMessage);
        }

        return new Result();
    }
}
```

The signature is the same as in a regular function. Note that there are no restrictions on visibility levels and static
modifiers, all of them can be used (`public`, `protected`, `private`, `static`).

Using a `callback` argument instead of `method` with PHP attributes is prohibited due to current PHP language
restrictions (a callback can't be inside a PHP attribute).

### For the whole object

It's also possible to check the whole object:

```php
use Exception;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\Callback;

#[Callback(method: 'validate')]
final class Config {
    public function __construct(        
        private int $yaml,
    ) {
    }

    private function validate(): Result 
    {
        if (!is_string($this->yaml)) {
            return (new Result())->addError('This value must be a string.');
        }
        
        $notYamlMessage = 'This value is not a valid YAML.';

        try {
            $data = yaml_parse($this->yaml);
        } catch (Exception $e) {
            return (new Result())->addError($notYamlMessage);
        }
        
        if ($data === false) {
            return (new Result())->addError($notYamlMessage);
        }

        return new Result();
    }
}
```

Note the use of property value (`$this->yaml`) instead of method argument (`$value`).

## Using a callable class

A class that implements `__invoke()` can also be used as a callable:

```php
use Exception;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\Callback;
use Yiisoft\Validator\ValidationContext;

final class YamlCallback
{    
    public function __invoke(mixed $value): Result
    {
        if (!is_string($value)) {
            return (new Result())->addError('This value must be a string.');
        }
        
        $notYamlMessage = 'This value is not a valid YAML.';

        try {
            $data = yaml_parse($value);
        } catch (Exception $e) {
            return (new Result())->addError($notYamlMessage);
        }
        
        if ($data === false) {
            return (new Result())->addError($notYamlMessage);
        }

        return new Result();
    }
}
```

The signature is the same as in a regular function.

Using in rules (note that a new instance must be passed, not a class name):

```php
use Yiisoft\Validator\Rule\Callback;

$rules = [
    'yaml' => new Callback(new YamlCallback()),
];
``` 

## Shortcut for use with validator

When using with the validator and default `Callback` rule settings, a rule declaration can be omitted, so just including a
callable is enough. It will be normalized automatically before validation:

```php
use Exception;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Validator;

$data = [];
$rules = [
    'yaml' => static function (mixed $value): Result {
        if (!is_string($value)) {
            return (new Result())->addError('This value must be a string.');
        }

        $notYamlMessage = 'This value is not a valid YAML.';

        try {
            $data = yaml_parse($value);
        } catch (Exception $e) {
            return (new Result())->addError($notYamlMessage);
        }

        if ($data === false) {
            return (new Result())->addError($notYamlMessage);
        }

        return new Result();
    },
];
$result = (new Validator())->validate($data, $rules);
```

Or it can be set within an array of other rules:

```php
use Exception;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\Validator;

$data = [];
$rules = [
    'yaml' => [
        new Required(),
        static function (mixed $value): Result {
            if (!is_string($value)) {
                return (new Result())->addError('This value must be a string.');
            }
        
            $notYamlMessage = 'This value is not a valid YAML.';
        
            try {
                $data = yaml_parse($value);
            } catch (Exception $e) {
                return (new Result())->addError($notYamlMessage);
            }
        
            if ($data === false) {
                return (new Result())->addError($notYamlMessage);
            }
        
            return new Result();
        },
    ],    
];
$result = (new Validator())->validate($data, $rules);
```

[YAML]: https://en.wikipedia.org/wiki/YAML
[yaml_parse docs]: https://www.php.net/manual/en/function.yaml-parse.php
