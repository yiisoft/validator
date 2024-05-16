# Client side validation

Unlike Yii2, the package does not provide any handling of the validation rules on the client side. Likely it will be 
added later as another related package. 

However, there is a possibility to export rules' options as an array for passing to the client side using 
`RulesDumper` class:

- Multiple rules and nesting of rules are supported.
- If a rule does not provide options, only the name is exported.
- The option values that can't be serialized/reproduced on the client side - [`callables`], for example, are excluded - 
either completely like `Callback::$callback` or partially like `$skipOnEmpty` if multiple types are supported.

Given built-in `Length` rule:

```php
use Yiisoft\Validator\Helper\RulesDumper;
use Yiisoft\Validator\Rule\Length;

$rules = [  
    'name' => [  
        new Length(min: 4, max: 10),  
    ],  
];  
$options = RulesDumper::asArray($rules);
```

the output will be:

```php
[  
    'name' => [  
        [  
            'length',  
            'min' => 4,  
            'max' => 10,  
            'exactly' => null,  
            'lessThanMinMessage' => [  
                'template' => 'This value must contain at least {min, number} {min, plural, one{character} other{characters}}.',  
                'parameters' => ['min' => 4],  
            ],  
            'greaterThanMaxMessage' => [  
                'template' => 'This value must contain at most {max, number} {max, plural, one{character} other{characters}}.',  
                'parameters' => ['max' => 10],  
            ],  
            'notExactlyMessage' => [  
                'template' => 'This value must contain exactly {exactly, number} {exactly, plural, one{character} other{characters}}.',  
                'parameters' => ['exactly' => null],  
            ],  
            'incorrectInputMessage' => [  
                'template' => 'The value must be a string.',  
                'parameters' => [],  
            ],  
            'encoding' => 'UTF-8',  
            'skipOnEmpty' => false,  
            'skipOnError' => false,  
        ],
    ],  
],
```

The resulting array, serialized as JSON, can be unserialized back and applied to an implementation of your choice.

## Structure of exported options

Here are some specifics of the rules structure:

- The indexing of rules by attribute names is maintained.
- The first rule element is always a rule name with an integer index of `0`.
- The remaining rule elements are key-value pairs, where key is an option name and value is a corresponding option value.
- For complex rules, such as [`Composite`], [`Each`] and [`Nested`], the options of the child rules are located under
  the `rules` key.

Note that the error messages have a special structure:

```php
[
    'lessThanMinMessage' => [  
        'template' => 'This value must contain at least {min, number} {min, plural, one{character} other{characters}}.',  
        'parameters' => ['min' => 4],  
    ],
];
```

It stays the same regardless of the presence of placeholders and parameters:

```php
'message' => [
    'template' => 'Value is invalid.',
    'parameters' => [],
],
```

[`callables`]: https://www.php.net/manual/en/language.types.callable.php
[`Nested`]: built-in-rules-nested.md
[`Each`]: built-in-rules-each.md
[`Composite`]: built-in-rules-composite.md
