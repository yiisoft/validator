# `Callback` - a wrapper over `callable`

This rule allows validation of current attribute value (but not limited to it) with an arbitrary condition set within a 
callable. The benefit of it is that creation of a separate custom rule and handler is not required.

A condition can be within:

- Standalone callable function.
- Callable class.
- DTO method.

The downside drown from using standalone functions and DTO methods is a lack of reusability. So they are mainly useful 
for some specific non-repetitive conditions. Reusability can be achieved with callable classes, but depending on other
factors (the need for additional parameters for example), it might be a good idea to create a full-fledged custom rule 
with a separate handler instead.

The signature of the function is like the following:

```php
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\Callback;
use Yiisoft\Validator\ValidationContext;

function (mixed $value, Callback $rule, ValidationContext $context): Result;
```

where:

- `$value` is validated value;
- `$rule` is a reference to original `Callback` rule;
- `$context` is a validation context;
- returned value is a validation result instance with errors or without them.

## Using as a function

An example of passing a standalone callable function to `Callback` rule:

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

A validation context can be utilized too - for example, when performing validation of attributes depending on each
other. In the below example the 3 angles are verified by degree values to form a valid triangle:

```php
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\Callback;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\ValidationContext;

$rules = [
    'angleA' => [
        new Required(),
        new Number(integerOnly: true),
    ],
    'angleB' => [
        new Required(),
        new Number(integerOnly: true),
    ],
    'angleC' => [
        new Required(),
        new Number(integerOnly: true),
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

They can be rewritten using multiple rules (built-in ones if possible) and conditional validation making code less 
verbose and more intuitive:

```php
use Yiisoft\Validator\Rule\BooleanValue;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\ValidationContext;

$rules = [
    'married' => new BooleanValue(),
    'spouseAge' => new Number(
        integerOnly: true,
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

When using as PHP attribute, set an object's method as a callback instead:

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

The signature is the same as in a regular function. Note that there are no limitations for visibility levels and static 
modifiers, all of them can be used (`public`, `protected`, `private`, `static`).

Using a `callback` argument instead of `method` with PHP attributes is prohibited because of the current PHP language 
limitations (a callback can't be inside PHP attribute). 

### For the whole object

It's also possible to check the object as a whole:

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

Note using property value (`$this->yaml`) instead of method argument (`$value`).

## Using a callable class

A classes implementing `__invoke()` can be used as a callable too:

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

## Shortcut for using with validator

When using with validator and default `Callback` rule settings, a rule declaration can be omitted, so including only a 
callable is enough. It will be automatically normalized before validation:

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
